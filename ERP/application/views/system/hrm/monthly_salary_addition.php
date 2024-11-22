
<!--Translation added by Naseek-->

<?php


$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_payroll', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title = $this->lang->line('hrms_payroll_monthly_addition');
echo head_page($title, false);


$currency_arr = all_currency_drop();
$masterType_drop = system_salary_cat_drop('VPG', 1);
$com_currency = $this->common_data['company_data']['company_default_currency'];
$date_format_policy = date_format_policy();
?>

<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">
        <table class="table table-bordered table-striped table-condensed ">
            <tbody>
                <tr>
                    <td>
                        <span class="label label-success" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> <?php echo $this->lang->line('common_confirmed');?><!--Confirmed -->/<?php echo $this->lang->line('common_approved');?><!--Approved-->
                    </td>
                    <td>
                        <span class="label label-danger" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> <?php echo $this->lang->line('common_not_confirmed');?><!--Not Confirmed-->/ <?php echo $this->lang->line('common_not_approved');?><!--Not Approved-->
                    </td>
                    <td>
                        <span class="label label-warning" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span>  <?php echo $this->lang->line('common_refer_back');?><!--Refer-back-->
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <!--<div class="col-md-2 pull-right">
        <button type="button" onclick="open_additionModel()" class="btn btn-primary btn-sm pull-right" ><i class="fa fa-plus"></i> Create New </button>
    </div>-->
    <div class="col-md-2 pull-right">
        <div class="clearfix hidden-lg">&nbsp;</div>
        <button type="button" class="btn btn-primary btn-sm pull-right" onclick="open_additionModel()"><i class="fa fa-plus"></i> <?php echo $this->lang->line('common_create_new');?><!--Create New--> </button>
    </div>
    <div class="col-md-5">
        <form class="form-inline pull-right">
            <div class="form-group">
                <label for="isNonPayroll"><?php echo $this->lang->line('hrms_payroll_payroll_type');?><!--Payroll Type--> &nbsp;</label>
                <select name="isNonPayroll" id="isNonPayroll" class="form-control" onchange="monthlyAdditionMasterView()">
                    <option value="N"><?php echo $this->lang->line('hrms_payroll_payroll');?><!--Payroll--></option>
                    <option value="Y"><?php echo $this->lang->line('hrms_payroll_non_payroll');?><!--Non payroll--></option>
                </select>
            </div>
        </form>
        <form class="form-inline pull-right">
            <div class="form-group" style="margin-right: 20px;">
                <label for="isNonPayroll"><?php echo $this->lang->line('common_type');?> &nbsp;</label>
                <select name="filterType" id="filterType" class="form-control" onchange="monthlyAdditionMasterView()">
                    <option value="" selected>All</option>
                    <option value="0" selected>Monthly Addition</option>
                    <option value="1" selected>Variable Addition</option>
                    <?php
                    $option = '';
                    if(!empty($masterType_drop)){
                        foreach($masterType_drop as $key=>$val){
                            $option .= "<option value='{$key}' >{$val}</option>";
                        }
                    }
                    echo $option;
                    ?>
                </select>
            </div>
        </form>
    </div>
</div>

<hr>

<div class="row">
<div class="col-sm-12">
    <div class="table-responsive">
        <table id="additionMaster_table" class="<?php echo table_class(); ?>">
            <thead>
                <tr>
                    <th style="width: 5%">#</th>
                    <th style="width: 10%"><?php echo $this->lang->line('common_code');?><!--Code--></th>
                    <th style="width: 15%"><?php echo $this->lang->line('common_type');?></th>
                    <th style="width: 30%"><?php echo $this->lang->line('common_description');?><!--Description--></th>
                    <th style="width: 10%"><?php echo $this->lang->line('common_date');?><!--Date--></th>
                    <th style="width: 10%"><?php echo $this->lang->line('common_status');?><!--Status--></th>
                    <th style="width: 5%">&nbsp;</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
</div>


<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div class="modal fade" id="addMaster_model" role="dialog" data-keyboard="false" data-backdrop="static" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title"><?php echo $this->lang->line('hrms_payroll_new_monthly_addition');?><!--New Monthly Addition--></h3>
            </div>
            <form role="form" id="monthlyAdditionMaster_form" class="form-horizontal">
                <div class="modal-body">
                    <!--<div class="form-group">
                        <label class="col-sm-4 control-label">Currency</label>
                        <div class="col-sm-4">
                            <?php /*echo form_dropdown('comCurrency', $currency_arr, $com_currency,'class="form-control" id="empCurrencySelect" disabled'); */?>
                        </div>
                    </div>-->
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_type');?> <?php required_mark(); ?></label>
                        <div class="col-sm-6">
                            <select name="systemType" class="form-control" id="systemType" onchange="getDescription()">
                                <option value="0" selected>Monthly Addition</option>
                                <option value="1">Variable Addition</option>
                                <?php
                                $option = '';
                                if(!empty($masterType_drop)){
                                    foreach($masterType_drop as $key=>$val){
                                        $option .= "<option value='{$key}' >{$val}</option>";
                                    }
                                }
                                echo $option;
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_date');?><!--Date--></label>
                        <div class="col-sm-6">
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="dateDesc" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"  id="dateDesc"
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
                        <label class="col-sm-4 control-label" for="payrollType"><?php echo $this->lang->line('hrms_payroll_payroll_type');?><!--Payroll Type --><?php required_mark(); ?></label>
                        <div class="col-sm-6">
                            <select name="payrollType" id="payrollType" class="form-control">
                                <option value="N"><?php echo $this->lang->line('hrms_payroll_payroll');?><!--Payroll--></option>
                                <option value="Y"><?php echo $this->lang->line('hrms_payroll_non_payroll');?><!--Non payroll--></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group hide" id="pullAttandanceCheck">
                        <label class="col-sm-4 control-label" for="isPullFromAttandance"><?php echo $this->lang->line('common_pull_from_attendance');?><!--Description--> <?php required_mark(); ?></label>
                        <div class="col-sm-6">
                            <input type="checkbox" name="isPullFromAttandance"  id="isPullFromAttandance" value="1">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    <button type="submit" class="btn btn-primary btn-sm saveBtn submitBtn" data-value="0"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    var isNonPayrollFilter = window.localStorage.getItem('mAddition-filter');
    var systemTypeFilter = window.localStorage.getItem('mAddition-filter-system-type');
    isNonPayrollFilter = (isNonPayrollFilter == null)? 'N' : isNonPayrollFilter;
    systemTypeFilter = (systemTypeFilter == null)? 0 : systemTypeFilter;
    $("#isNonPayroll").val(isNonPayrollFilter);
    $("#filterType").val(systemTypeFilter);


    $( document ).ready(function() {
        $('.headerclose').click(function(){
            fetchPage('system/hrm/monthly_salary_addition', '','HRMS');
        });

        monthlyAdditionMasterView();

        Inputmask().mask(document.querySelectorAll("input"));

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            $('#monthlyAdditionMaster_form').bootstrapValidator('revalidateField', 'dateDesc');

            getDescription();
        });

        $('#monthlyAdditionMaster_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                monthDescription: {validators: {notEmpty: {message: 'Description is required.'}}},
                dateDesc: {validators: {notEmpty: {message: 'Date is required.'}}},
            },
        })
        .on('success.form.bv', function (e) {
            $('.submitBtn').prop('disabled', false);
            e.preventDefault();
            var $form      = $(e.target);
            var bv         = $form.data('bootstrapValidator');
            var data       = $form.serializeArray();
            var isConform  = $('#isConform').val();

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Employee/save_monthAddition'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if(data[0] == 's'){
                        $("#addMaster_model").modal("hide");

                        var payrollType = $('#payrollType').val();
                        var systemType =  $('#systemType').val();
                        window.localStorage.setItem('mAddition-filter', payrollType);
                        payrollType = ( payrollType == 'Y' )? 2 : 1;

                        setTimeout(function(){
                            if(systemType == 1){
                                fetchPage('system/hrm/emp_monthly_variable_salary_addition', data[2], 'HRMS','', payrollType);
                            }else{
                                fetchPage('system/hrm/emp_monthly_salary_addition', data[2], 'HRMS','', payrollType);
                            }
                           
                        }, 300);


                    }
                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });


        });

    });

    function monthlyAdditionMasterView(selectedID=null) {
        var isNonPayroll = $("#isNonPayroll").val();
        var filterType = $("#filterType").val();
        window.localStorage.setItem('mAddition-filter', isNonPayroll);
        window.localStorage.setItem('mAddition-filter-system-type', filterType);

        var selectedRowID = (selectedID == null)? parseInt('<?php echo $this->input->post('page_id'); ?>') : parseInt(selectedID);
        $('#additionMaster_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/load_monthlyAdditionMaster_table'); ?>",
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

            },
            "aoColumns": [
                {"mData": "masterID"},
                {"mData": "monthlyAdditionsCode"},
                {"mData": "typeDescription"},
                {"mData": "des"},
                {"mData": "dateMA"},
                {"mData": "status"},
                {"mData": "action"}
            ],
            "columnDefs": [{
                "targets": [0,5,6],
                "orderable": false
            }, {"searchable": false, "targets": [0,5]}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({ "name": "isNonPayroll","value": isNonPayroll});
                aoData.push({ "name": "filterType","value": filterType});
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

    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });


    function getDescription(){
        var dateDesc = $('#dateDesc').val();
        var systemType = $('#systemType').val();

        if(systemType == 1){
            $('#pullAttandanceCheck').removeClass('hide');
        }else{
            $('#pullAttandanceCheck').addClass('hide');
        }  


        if(systemType == '' || dateDesc == ''){
            return false;
        }
    
        
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: { 'dateDesc':dateDesc, typeMonthly:'MA', 'systemType':systemType },
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

    function getConformation(data, requestUrl){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document');?>",/*You want to confirm this document*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes!*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                save(data, requestUrl);
            }
        );
    }

    function open_additionModel() {
        $('#isConform').val(0);
        $('#monthlyAdditionMaster_form')[0].reset();
        $('#monthlyAdditionMaster_form').bootstrapValidator('resetForm', true);
        $(".modal-title").text('<?php echo $this->lang->line('hrms_payroll_new_monthly_addition')?>');/*New Monthly Addition*/
        $("#addMaster_model").modal({backdrop: "static"});
        $('.submitBtn').prop('disabled', false);
        $('#systemType').val(0);

        btnHide('saveBtn', 'updateBtn');
    }

    function btnHide(btn1, btn2){
        $('.'+btn1).show();
        $('.'+btn2).hide();
    }

    function delete_details(delID, code){
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
                    url: "<?php echo site_url('Employee/delete_monthAddition'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        monthlyAdditionMasterView();
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
                    data: { 'referID': id, 'referBack': 'MA' },
                    url: "<?php echo site_url('Employee/referBack_monthAddition'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if( data[0] == 's'){
                            monthlyAdditionMasterView(id);
                        }
                    }, error: function () {
                        myAlert('e', 'An Error Occurred! Please Try Again.');
                        stopLoad();
                    }
                 });
            }
        );
    }

</script>









<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 5/30/2016
 * Time: 11:31 AM
 */
