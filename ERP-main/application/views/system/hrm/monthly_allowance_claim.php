<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_payroll', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title ='Monthly Allowance Claim';
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
    <div class="col-md-1 pull-right">
        <div class="clearfix hidden-lg">&nbsp;</div>
        <button type="button" class="btn btn-primary btn-sm pull-right" onclick="open_additionModel()"><i class="fa fa-plus"></i> <?php echo $this->lang->line('common_create_new');?><!--Create New--> </button>
    </div>
    <div class="col-md-6">
        <form class="form-inline pull-right">
            <label class=" control-label">From Date</label>
            <div class="input-group datepic">
                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                <input type="text" name="fromDateFilter" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"  id="fromDateFilter"
                    class="form-control" required>
            </div>
            <label class="control-label">To Date</label>
            <div class="input-group datepic">
                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                <input type="text" name="toDateFilter" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"  id="toDateFilter" 
                    class="form-control" required>
            </div>
            <button type="button" class="btn btn-primary btn-sm pull-right" onclick="load_monthlyAllowanceClaimMaster_table()"><i class="fa fa-search"></i>&nbsp;load</button>
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
                    <th style="width: 20%"><?php echo $this->lang->line('common_description');?><!--Description--></th>
                    <th style="width: 10%">Document Date<!--Date--></th>
                    <th style="width: 10%">From Date<!--From Date--></th>
                    <th style="width: 10%">To Date<!--To Date--></th>
                    <th style="width: 5%">Confirmed</th>
                    <th style="width: 5%">Approved</th>
                    <th style="width: 10%">&nbsp;</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
</div>


<div class="modal fade" id="addMaster_model" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">Monthly Addition Claim</h3>
            </div>
            <form role="form" id="monthlyAllowance_form" class="form-horizontal">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Document Date</label>
                        <div class="col-sm-6">
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="documentDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" id="documentDate" 
                                       class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_description');?><!--Description--></label>
                        <div class="col-sm-6">
                            <input type="text" id="monthDescription" name="monthDescription" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label">From</label>
                        <div class="col-sm-6">
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="fromDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" id="fromDate"
                                       class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label">To</label>
                        <div class="col-sm-6">
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="toDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" id="toDate"
                                       class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Frequency</label>
                        <div class="col-sm-6">
                        <select name="frequency" class="form-control" id="frequency">
                            <option value="">Select a frequency</option>
                            <option value="1">Monthly</option>
                            <option value="2">Quarterly</option>
                            <option value="3">Half-yearly</option>
                            <option value="4">Annually</option>
                        </select>
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
<?php echo footer_page('Right foot', 'Left foot', FALSE); ?>
<script type="text/javascript">
     var from_date_filter = window.localStorage.getItem('from_date_filter');
     var to_date_filter = window.localStorage.getItem('to_date_filter');
     from_date_filter = (from_date_filter == null)? '' : from_date_filter;
     to_date_filter = (to_date_filter == null)? '' : to_date_filter;
    $("#fromDateFilter").val(from_date_filter);
    $("#toDateFilter").val(to_date_filter);

    var payrollType = 'N';
    var systemType = 0;

    $( document ).ready(function() {
        $('.headerclose').click(function(){
            fetchPage('system/hrm/monthly_allowance_claim', '','HRMS');
        });

        $('#fromDateFilter').val('');
        $("#toDateFilter").val('');

        load_monthlyAllowanceClaimMaster_table();

        Inputmask().mask(document.querySelectorAll("input"));

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            $('#monthlyAllowance_form').bootstrapValidator('revalidateField', 'dateDesc');

           // getDescription();
        });

        $('#monthlyAllowance_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                monthDescription: {validators: {notEmpty: {message: 'Description is required.'}}},
                fromDate: {validators: {notEmpty: {message: 'From Date is required.'}}},
                toDate: {validators: {notEmpty: {message: 'To Date is required.'}}},
            },
        })
        .on('success.form.bv', function (e) {
            $('.submitBtn').prop('disabled', false);
            e.preventDefault();
            var $form      = $(e.target);
            var bv         = $form.data('bootstrapValidator');
            var data       = $form.serializeArray();

            // var isConform  = $('#isConform').val();

            // var monthDescription = $('#monthDescription').val();
            // data.push({ "name": "monthDescription","value": encodeURIComponent(monthDescription)});

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Employee/save_monthly_allowance_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if(data[0] == 's'){
                        $("#addMaster_model").modal("hide");

                        setTimeout(function(){
                            if(systemType == 0){
                                //fetchPage('system/hrm/emp_monthly_salary_addition', data[2], 'HRMS','', payrollType);
                                fetchPage('system/hrm/monthly_allowance_claim_edit', data[2], 'HRMS','', payrollType);
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

    function load_monthlyAllowanceClaimMaster_table(selectedID=null) {
        var fromDateFilter = $("#fromDateFilter").val();
        var toDateFilter = $("#toDateFilter").val();
        window.localStorage.setItem('from_date_filter', fromDateFilter);
        window.localStorage.setItem('to_date_filter', toDateFilter);

        var selectedRowID = (selectedID == null)? parseInt('<?php echo $this->input->post('page_id'); ?>') : parseInt(selectedID);
        $('#additionMaster_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/load_monthlyAllowanceClaimMaster_table'); ?>",
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
                {"mData": "monthlyClaimCode"},
                {"mData": "typeDescription"},
                {"mData": "des"},
                {"mData": "documentDate"},
                {"mData": "fromDate"},
                {"mData": "toDate"},
                {"mData": "status"},
                {"mData": "approved"},
                {"mData": "action"}
            ],
            "columnDefs": [{
                "targets": [0,7,8],
                "orderable": false
            }, {"searchable": false, "targets": [0,7]}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({ "name": "fromDateFilter","value": fromDateFilter});
                aoData.push({ "name": "toDateFilter","value": toDateFilter});
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
        //$('#isConform').val(0);
        $('#monthlyAllowance_form')[0].reset();
        $('#monthlyAllowance_form').bootstrapValidator('resetForm', true);
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
                    url: "<?php echo site_url('Employee/delete_monthAllowance'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        load_monthlyAllowanceClaimMaster_table();
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
                    data: { 'referID': id},
                    url: "<?php echo site_url('Employee/referBack_month_Allowance'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if( data[0] == 's'){
                            load_monthlyAllowanceClaimMaster_table(id);
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

