<!--Translation added by Naseek-->

<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_payroll', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title = $this->lang->line('hrms_payroll_monthly_deduction');
echo head_page($title, false);

$currency_arr = all_currency_drop();
$pGroups_drop = payroll_group_drop();
$date_format_policy = date_format_policy();
$com_currency = $this->common_data['company_data']['company_default_currency'];
?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">
        <table class="table table-bordered table-striped table-condensed ">
            <tbody><tr>
                <td>
                    <span class="label label-success" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> <?php echo $this->lang->line('common_confirmed');?><!--Confirmed -->/<?php echo $this->lang->line('common_approved');?><!--Approved-->
                </td>
                <td>
                    <span class="label label-danger" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> <?php echo $this->lang->line('common_not_confirmed');?><!-- Not Confirmed-->/ <?php echo $this->lang->line('common_not_approved');?><!--Not Approved-->
                </td>
                <td>
                    <span class="label label-warning" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> <?php echo $this->lang->line('common_refer_back');?><!--Refer-back-->
                </td>
            </tr>
            </tbody></table>
    </div>
    <div class="col-md-2 pull-right">
        <div class="clearfix hidden-lg">&nbsp;</div>
        <button type="button" class="btn btn-primary btn-sm pull-right" onclick="open_DeductionModel()"><i class="fa fa-plus"></i> <?php echo $this->lang->line('common_create_new');?><!--Create New--> </button>
    </div>
    <div class="col-md-5">
        <form class="form-inline pull-right">
            <div class="form-group">
                <label for="isNonPayroll"> <?php echo $this->lang->line('hrms_payroll_payroll_type');?><!--Payroll Type--> &nbsp;</label>
                <select name="isNonPayroll" id="isNonPayroll" class="form-control" onchange="monthlyDeductionMasterView()">
                    <option value="N"><?php echo $this->lang->line('hrms_payroll_payroll');?><!--Payroll--></option>
                    <option value="Y"><?php echo $this->lang->line('hrms_payroll_non_payroll');?><!--Non payroll--></option>
                </select>
            </div>
        </form>
    </div>
</div>
<hr>

<div class="table-responsive">
    <table id="monthlyDeductionTB" class="<?php echo table_class(); ?>">
        <thead>
            <tr>
                <th style="width: 5%">#</th>
                <th style="width: 15%"><?php echo $this->lang->line('common_code');?><!--Code--></th>
                <th style="width: 12%"><?php echo $this->lang->line('common_payroll_group');?></th>
                <th style="width: 30%"><?php echo $this->lang->line('common_description');?><!--Description--></th>
                <th style="width: 8%"><?php echo $this->lang->line('common_date');?><!--Date--></th>
                <th style="width: 8%"><?php echo $this->lang->line('common_status');?><!--Status--></th>
                <th style="width: 70px">&nbsp;</th>
            </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div class="modal fade" id="addMaster_model" role="dialog" data-keyboard="false" data-backdrop="static" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title"><?php echo $this->lang->line('hrms_payroll_new_monthly_deductions');?><!--New Monthly Deductions--></h3>
            </div>
            <form role="form" id="monthlyDeductionMaster_form" class="form-horizontal">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_payroll_group');?> <?php required_mark(); ?></label>
                        <div class="col-sm-6">
                            <?=form_dropdown('p_group', $pGroups_drop, null, 'class="form-control" id="p_group"')?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_date');?><!--Date--></label>
                        <div class="col-sm-6">
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="dateDesc" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="" id="dateDesc"
                                       class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_description');?><!--Description--></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="monthDescription" name="monthDescription">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="payrollType"><?php echo $this->lang->line('hrms_payroll_payroll_type');?><!--Payroll Type--> <?php required_mark(); ?></label>
                        <div class="col-sm-6">
                            <select name="payrollType" id="payrollType" class="form-control">
                                <option value="N"><?php echo $this->lang->line('hrms_payroll_payroll');?><!--Payroll--></option>
                                <option value="Y"><?php echo $this->lang->line('hrms_payroll_non_payroll');?><!--Non Payroll--></option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    <button type="submit" class="btn btn-primary btn-sm"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    var isNonPayrollFilter = window.localStorage.getItem('mDeduction-filter');
    isNonPayrollFilter = (isNonPayrollFilter == null)? 'N' : isNonPayrollFilter;
    $("#isNonPayroll").val(isNonPayrollFilter);

    $( document ).ready(function() {
        $('.headerclose').click(function(){
            fetchPage('system/hrm/monthly_salary_deduction_period_base','','HRMS');
        });
        monthlyDeductionMasterView();


        Inputmask().mask(document.querySelectorAll("input"));

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';


        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            $('#monthlyDeductionMaster_form').bootstrapValidator('revalidateField', 'dateDesc');
            getDescription();
        });
        $('#monthlyDeductionMaster_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                monthDescription: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_description_is_required');?>.'}}},/*Description is required*/
                dateDesc: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_date_is_required');?>.'}}},/*Date is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();

            data.push({'name': 'doc_type', 'value': 'MD'});

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Employee/save_monthlyMaster'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if(data[0] == 's'){
                        $("#addMaster_model").modal("hide");
                        setTimeout(function(){
                            var payrollType = $('#payrollType').val();
                            payrollType = ( payrollType == 'Y' )? 2 : 1;
                            fetchPage('system/hrm/emp_monthly_salary_deduction',data[2],'HRMS','', payrollType);
                        }, 2000);


                    }
                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        });

    });

    function monthlyDeductionMasterView(selectedID=null) {
        var isNonPayroll = $("#isNonPayroll").val();
        window.localStorage.setItem('mDeduction-filter', isNonPayroll);

        var selectedRowID = (selectedID == null)? parseInt('<?php echo $this->input->post('page_id'); ?>') : parseInt(selectedID);
        $('#monthlyDeductionTB').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('Employee/load_monthlyDeductionMaster_table'); ?>",
            "aaSorting": [[1, 'desc']],
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();

                var tmp_i   = oSettings._iDisplayStart;
                var iLen    = oSettings.aiDisplay.length;

                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);


                    if( parseInt(oSettings.aoData[x]._aData['masterID']) == selectedRowID ){
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }

                    x++;
                }
                $(".dataTables_empty").text('<?php echo $this->lang->line('common_no_data_available_in_table'); ?>')
                $(".previous a").text('<?php echo $this->lang->line('common_previous'); ?>')
                $(".next  a").text('<?php echo $this->lang->line('common_next'); ?>')
            },
            "aoColumns": [
                {"mData": "masterID"},
                {"mData": "monthlyDeductionCode"},
                {"mData": "payGroup"},
                {"mData": "description"},
                {"mData": "dateMD"},
                {"mData": "status"},
                {"mData": "action"}
            ],
            "columnDefs": [
                { "targets": [0,5,6], "orderable": false},
                {"searchable": false, "targets": [0,5]}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({ "name": "isPeriodBase","value": 'Y'});
                aoData.push({ "name": "isNonPayroll","value": isNonPayroll});
                $.ajax ({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }

    function getDescription(){
        var dateDesc = $('#dateDesc').val();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: { 'dateDesc':dateDesc, typeMonthly:'MD' },
            url: "<?php echo site_url('Employee/getDescriptionOfMonthlyAD'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#monthDescription').val(data);

            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function open_DeductionModel() {
        $('#isConform').val(0);
        $('#monthlyDeductionMaster_form')[0].reset();
        $('#monthlyDeductionMaster_form').bootstrapValidator('resetForm', true);
        $(".modal-title").text('<?php echo $this->lang->line('hrms_payroll_new_monthly_deductions');?>');/*New Monthly Deduction*/
        $("#addMaster_model").modal({backdrop: "static"});
        btnHide('saveBtn', 'updateBtn');
    }

    function btnHide(btn1, btn2){
        $('.'+btn1).show();
        $('.'+btn2).hide();
    }

    function edit_details(editID ){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: { 'editID': editID },
            url: "<?php echo site_url('Employee/edit_monthDeduction'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#isConform').val(0);
                $(".modal-title").text('Edit Monthly Deduction ['+data['monthlyDeductionCode']+']');
                $("#addMaster_model").modal({backdrop: "static"});
                $('#dateDesc').val(data['dateMD']);
                $('#monthDescription').val(data['description']);
                $('#updateCode').val(data['monthlyDeductionCode']);
                $('#updateID').val( editID );
                btnHide('updateBtn', 'saveBtn');

            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }


    function delete_details(delID){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                 $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: { 'delID': delID },
                    url: "<?php echo site_url('Employee/delete_monthDeduction'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        monthlyDeductionMasterView();
                    }, error: function () {
                        myAlert('e', 'An Error Occurred! Please Try Again.');
                        stopLoad();
                    }
                 });
            }
        );
    }

    function referBackConformation(id){
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
                    data: { 'referID': id, 'referBack': 'MD' },
                    url: "<?php echo site_url('Employee/referBack_monthAddition'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if( data[0] == 's'){
                            monthlyDeductionMasterView(id);
                        }
                    }, error: function () {
                        myAlert('e', 'An Error Occurred! Please Try Again.');
                        stopLoad();
                    }
                 });
            }
        );
    }

    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });

</script>






<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 5/30/2016
 * Time: 11:31 AM
 */
