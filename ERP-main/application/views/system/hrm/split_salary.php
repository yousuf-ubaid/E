<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('hrms_payroll_lang', $primaryLanguage);

$current_date = current_format_date();
$title = $this->lang->line('hrms_payroll_split_salary');
echo head_page($title, true);

$currency_arr = all_currency_new_drop();
$defaultCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
$date_format_policy = date_format_policy();
$doc_status = [
    'all' =>$this->lang->line('common_all'), '1' =>$this->lang->line('sales_markating_transaction_customer_draft'),
    '2' =>$this->lang->line('common_confirmed'), '3' =>$this->lang->line('common_approved'),'4'=>'Refer-back'
];
?>
    <link rel="stylesheet" href="<?php echo base_url('plugins/Horizontal-Hierarchical/src/jquery.hortree.css'); ?>"/>
    <div id="filter-panel" class="collapse filter-panel">
    </div>
    <div class="row">
        <div class="col-md-5">
            <table class="<?php echo table_class(); ?>">
                <tr>
                    <td><span class="label label-success">&nbsp;</span> <?php echo $this->lang->line('common_confirmed');?><!--Confirmed--> / <?php echo $this->lang->line('common_approved');?><!--Approved--></td>
                    <td><span class="label label-danger">&nbsp;</span> <?php echo $this->lang->line('common_not_confirmed');?><!--Not Confirmed--> / <?php echo $this->lang->line('common_not_approved');?><!--Not Approved--></td>
                    <td><span class="label label-warning">&nbsp;</span> <?php echo $this->lang->line('common_refer_back');?><!--Refer-back--></td>
                </tr>
            </table>
        </div>
        <div class="col-md-4 text-center">
            &nbsp;
        </div>
        <div class="col-md-3 text-right">
            <button type="button" class="btn btn-primary pull-right" onclick="create_split_salary()">
                <i class="fa fa-plus"></i> <?php echo $this->lang->line('common_update_add_new');?>
            </button>
        </div>
    </div><hr>
    <div class="table-responsive">
        <table id="split_salary_tb" class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 12%"><?php echo $this->lang->line('common_date');?></th><!--Date-->
                <th style="min-width: 15%"><?php echo $this->lang->line('common_code');?></th><!--Code-->
                <th style="min-width: 10%"><?php echo $this->lang->line('common_start_date');?></th><!--Start Date-->
                <th style="min-width: 10%"><?php echo $this->lang->line('common_end_date');?></th><!--End Date-->
                <th style="min-width: 30%"><?php echo $this->lang->line('common_description');?></th><!--Description-->
                <th style="min-width: 5%"><?php echo $this->lang->line('common_confirmed');?></th><!--Confirmed-->
                <th style="width: 5%"><?php echo $this->lang->line('common_approved');?></th><!--Approved-->
                <th style="width: 10%"><?php echo $this->lang->line('common_action');?></th><!--Action-->
            </tr>
            </thead>
        </table>
    </div>

    <div aria-hidden="true" role="dialog"  id="add_split_salary_modal" class=" modal fade bs-example-modal-lg"
         style="display: none;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h5 class="modal-title" id="categoryHead"></h5>
            </div>
            <?php echo form_open('', 'role="form" id="split_salary_form"'); ?>
            <div class="modal-body">
                <div class="row" style="margin: 5px">
                    <input type="hidden" id="splitSalaryMasterID" name="splitSalaryMasterID">
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label><?php echo $this->lang->line('common_currency');?><!--currency--> </label>
                        </div>
                        <div class="form-group col-md-6">
<!--                          --><?php /* echo form_dropdown('', $currency_arr, $defaultCurrencyID, 'class="form-control select2" id="splitCurrency" '); */?>
                          <?php echo form_dropdown('splitCurrency', $currency_arr, $defaultCurrencyID, 'class="form-control select2" id="splitCurrency"  required'); ?>

                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label><?php echo $this->lang->line('common_start_date');?><!--Start Date--> </label>
                        </div>
                        <div class="form-group col-md-6">
                            <span class="input-req" title="Required Field">
                                <div class="input-group datepic">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="splitStartDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                           value="<?php echo $current_date; ?>" id="splitStartDate" class="form-control" required>
                                </div>
                                <span class="input-req-inner" style="z-index: 100"></span>
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label><?php echo $this->lang->line('hrms_payroll_no_of_months');?><!--No Of Months --></label>
                        </div>
                        <div class="form-group col-md-6">
                            <input type="number" name="no_of_months" id="no_of_months" class="form-control" onchange="fetch_end_date(this.value)">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label><?php echo $this->lang->line('common_end_date');?><!--End date--> </label>
                        </div>
                        <div class="form-group col-md-6">
                            <span class="input-req" title="Required Field">
                                <div class="input-group datepic">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="splitEndDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                           value="<?php echo $current_date; ?>" id="splitEndDate" class="form-control" disabled required>
                                </div>
                                <span class="input-req-inner" style="z-index: 100"></span>
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label><?php echo $this->lang->line('common_description');?><!--Description --></label>
                        </div>
                        <div class="form-group col-md-6">
                            <textarea type="text" name="Description" id="Description" class="form-control"></textarea>
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary pull-right" type="submit"><?php echo $this->lang->line('common_create');?></button><!--Create-->
                    <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close');?> </button><!--Close--> &nbsp; &nbsp;
                </div>
                </form>
            </div>
        </div>
    </div>
    </div>

<?php echo footer_page('Right foot','Left foot',false); ?>
    <script type="text/javascript">
        var orderID = null;
        var split_tbl;
        $(document).ready(function() {
            $('.headerclose').click(function(){
                fetchPage('system/hrm/split_salary','','<?php echo $this->lang->line('hrms_payroll_split_salary'); ?>');
            });
            $('.select2').select2();
            $('.modal').on('hidden.bs.modal', function () {
                setTimeout(function () {
                    if ($('.modal').hasClass('in')) {
                        $('body').addClass('modal-open');
                    }
                }, 500);
            });

            var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

            $('.datepic').datetimepicker({
                useCurrent: false,
                format: date_format_policy,
            }).on('dp.change', function (ev) {

            });
            number_validation();
            split_salary_tbl();

            Inputmask().mask(document.querySelectorAll("input"));

            $('#split_salary_form').bootstrapValidator({
                live: 'enabled',
                message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
                excluded: [':disabled'],
                fields: {
                    splitCurrency: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_currency_is_required');?>.'}}},/*Currency is required*/
                    splitStartDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('hrms_payroll_start_date_is_required');?>.'}}},/*Start Date is required*/
                    no_of_months: {validators: {notEmpty: {message: 'No of months is required.'}}},/*Start Date is required*/
                    Description: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_description_is_required');?>.'}}}/*Description is required*/
                },
            }).on('success.form.bv', function (e) {
                e.preventDefault();
                $('#splitEndDate').prop('disabled', false);
                var $form = $(e.target);
                var bv = $form.data('bootstrapValidator');
                var data = $form.serializeArray();
                data.push({'name': 'splitSalaryMasterID', 'value': $('#splitSalaryMasterID').val()});
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Employee/create_split_salary'); ?>",
                    beforeSend: function () {
                        startLoad();
                        $('#splitEndDate').prop('disabled', true);
                    },
                    success: function (data) {
                        if(data[0]=='s'){
                            $('#split_salary_form')[0].reset();
                            $('#add_split_salary_modal').modal('hide');
                            myAlert(data[0],data[1]);
                            stopLoad();
                            split_tbl.draw();
                        }else{
                            stopLoad();
                            myAlert(data[0],data[1]);
                        }
                    },
                    error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                        stopLoad();
                    }
                });
            });

        });

        function split_salary_tbl(selectedID=null){
            split_tbl = $('#split_salary_tb').DataTable({
                "language": {
                    "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
                },
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "bStateSave": true,
                "sAjaxSource": "<?php echo site_url('Employee/fetch_split_salary'); ?>",
                "aaSorting": [[0, 'desc']],
                "fnInitComplete": function () {

                },
                "fnDrawCallback": function (oSettings) {
                    $("[rel=tooltip]").tooltip();
                    var selectedRowID = (selectedID == null)? parseInt('<?php echo $this->input->post('page_id'); ?>') : parseInt(selectedID);
                    var tmp_i   = oSettings._iDisplayStart;
                    var iLen    = oSettings.aiDisplay.length;
                    var x = 0;
                    for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                        if( parseInt(oSettings.aoData[x]._aData['DOAutoID']) == selectedRowID ){
                            var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                            $(thisRow).addClass('dataTable_selectedTr');
                        }
                        x++;
                    }
                    $('.deleted').css('text-decoration', 'line-through');
                    $('.deleted div').css('text-decoration', 'line-through');
                },
                "aoColumns": [
                    {"mData": "splitSalaryMasterID"},
                    {"mData": "documentDate"},
                    {"mData": "splitSalaryCode"},
                    {"mData": "startDate"},
                    {"mData": "endDate"},
                    {"mData": "description"},
                    {"mData": "confirmed"},
                    {"mData": "approved"},
                    {"mData": "edit"}
                ],
                "columnDefs": [{"targets": [6], "orderable": false},{"targets": [0,2,3], "visible": true,"searchable": false}],
                "fnServerData": function (sSource, aoData, fnCallback) {
                    // aoData.push({"name": "date_from", "value": $("#filter_date_from").val()});
                    // aoData.push({"name": "date_to", "value": $("#filter_date_to").val()});
                    // aoData.push({"name": "status", "value": $("#status").val()});
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

        function delete_split_salary(id){
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record*/
                    type: "warning",/*warning*/
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
                        data : {'masterID':id},
                        url :"<?php echo site_url('Employee/delete_split_salary'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success : function(data){
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                split_tbl.draw();
                            }
                        },error : function(){
                            myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                            stopLoad();
                        }
                    });
                });
        }

        function refer_back_delivery_order(id){
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*/!*Are you sure?*!/*/
                    text: "<?php echo $this->lang->line('common_you_want_to_refer_back');?>",/*You want to refer back!*/
                    type: "warning",/*warning*/
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async : true,
                        type : 'post',
                        dataType : 'json',
                        data : {'DOAutoID':id},
                        url :"<?php echo site_url('Delivery_order/refer_back_delivery_order'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success : function(data){
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                split_tbl.draw();
                            }
                        },error : function(){
                            myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                            stopLoad();
                        }
                    });
                });
        }

        function reOpen_splitSalary(id){
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_re_open');?>",/*You want to re open!*/
                    type: "warning",/*warning*/
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async : true,
                        type : 'post',
                        dataType : 'json',
                        data : {'masterID':id},
                        url :"<?php echo site_url('Employee/reOpen_split_salary'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success : function(data){
                            split_tbl.draw();
                            stopLoad();
                            refreshNotifications(true);
                        },error : function(){
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

        function create_split_salary() {
            $('#splitSalaryMasterID').val('');
            $('#splitCurrency').val(<?php echo $defaultCurrencyID; ?>).change();
            $('#categoryHead').html('<?php echo $this->lang->line('hrms_payroll_split_salary');?>');/*Split Salary*/
            $('#split_salary_form')[0].reset();
            $('#add_split_salary_modal').modal('show');
        }

        function fetch_end_date(noOfMonths) {
            var startdate = $('#splitStartDate').val();
            var newdate = startdate.split("-").reverse().join("-");
            var newstartdate = new Date("'" + newdate + "'");
            var endDate = new Date(newstartdate.setMonth(Number(newstartdate.getMonth()) + Number(noOfMonths)));
            var date = new Date(endDate),
                mnth = ("0" + (date.getMonth() + 1)).slice(-2),
                day = ("0" + date.getDate()).slice(-2);
            var enddateReverse = [date.getFullYear(), mnth, day].join("-");
            var newENDdate = enddateReverse.split("-").reverse().join("-");
            $('#splitEndDate').val(newENDdate);
        }

        function referback_splitSalary(id) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                    text: "<?php echo $this->lang->line('common_you_want_to_refer_back');?>",/*You want to refer back!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55 ",
                    confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes!*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'splitSalaryMasterID': id},
                        url: "<?php echo site_url('Employee/referback_split_salary'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                split_tbl.draw();
                            }
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    </script>

