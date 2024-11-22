<!--Translation added by Naseek-->
<?php


$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_over_time', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title = $this->lang->line('hrms_over_time_ot_monthly_addition_details');
echo head_page($title, false);

$currency_arr = all_currency_new_drop();
$otGroup_arr = OT_group_dropDown();
$com_currency = $this->common_data['company_data']['company_default_currency'];
$com_decPlace = $this->common_data['company_data']['company_default_decimal'];
$date_format_policy = date_format_policy();
$current_date = current_format_date();
?>

    <style type="text/css">
        .trInputs{
            width: 100%;
            padding: 2px 2px;
            height: 20px;
        }
        .empCurrencySelect{
            padding-top: 3px ;
            padding-bottom: 3px ;
            width: auto;
        }

        .dateFields {
            z-index: 100 !important;
        }

        .empAddBtn{
            font-size: 10px;
            padding: 3px 10px;
        }

        .required-class{ border: 1px solid #e20b1f;}
        .remove-required-class{ border: 1px solid #a9a9a9;}

        .select2-container--default .select2-selection--single, .select2-selection .select2-selection--single{
            height: 22px;
            padding: 0px 5px
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow{ height: 18px !important;}

        .hideTr{ display: none }

        .oddTR td{ background: #f9f9f9 !important; }

        .evenTR td{ background: #ffffff !important; }
    </style>

<?php echo form_open('', 'id="emp_monthlyAddFrm" autocomplete="off"') ?>
    <div class="col-md-6">
        <div class="box box-info" style="padding-top: 3%">
            <div class="form-horizontal">
                <div class="box-body">
                    <div class="form-group">
                        <label for="documentCode" class="col-sm-3 control-label"><?php echo $this->lang->line('hrms_over_time_document_code');?><!--Document Code--></label>

                        <div class="col-sm-8">
                            <input type="text" name="documentCode" class="form-control"  id="documentCode" disabled>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="desDate" class="col-sm-3 control-label"><?php echo $this->lang->line('common_date');?><!--Date--></label>

                        <div class="col-sm-8">
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="dateDesc" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                       value="<?php echo $current_date; ?>" id="desDate" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="box box-info" style="padding-top: 3%">
            <div class="form-horizontal">
                <div class="box-body">
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="currencyID"><?php echo $this->lang->line('common_currency');?><!--Currency--></label>
                        <div class="col-sm-8">
                            <?php echo form_dropdown('currencyID', $currency_arr, '', 'class="form-control select2" id="currencyID" disabled'); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description" class="col-sm-3 control-label"><?php echo $this->lang->line('common_description');?><!--Description--></label>
                        <div class="col-sm-8">
                            <input type="text" name="monthDescription" class="form-control" id="description" placeholder="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="detail-table-container"> </div>

    <div class="row"> <!--modal-footer-->
        <hr>
        <div class="col-sm-6 pull-left">
            <!--<div class="form-group">
            <label class="col-sm-3 control-label">Total Amount</label>
            <div class="col-sm-5">
                <div class="input-group">
                    <div class="input-group-addon">( <?php /*echo $com_currency */?> )</div>
                    <input type="text" id="totalAmount" placeholder="0.00" class="form-control number" readonly>
                </div>
            </div>
        </div>-->
        </div>

        <div class="col-sm-3 pull-right">
            <input type="hidden" id="rowCount" value="0">
            <input type="hidden" id="isConfirm" name="isConfirm" value="0">
            <input type="hidden" id="updateID" name="updateID" value="">
            <input type="hidden" id="updateCode" name="updateCode" value="">

            <button type="button" class="btn btn-success btn-sm saveBtn submitBtn pull-right" data-value="1" style=""><?php echo $this->lang->line('common_save_and_confirm');?><!--Save & Confirm--></button>
            <button type="button" class="btn btn-primary btn-sm saveBtn submitBtn pull-right" data-value="0" style="margin-right: 5px;"><?php echo $this->lang->line('common_save_as_draft');?><!--Save As Draft--></button>
        </div>
    </div>




<?php echo form_close();?>

<?php echo footer_page('Right foot','Left foot',false); ?>
<div class="modal fade" id="employee_model" role="dialog" data-keyboard="false" data-backdrop="static"  style="z-index: 999999" >
    <div class="modal-dialog modal-lg" style="width: 95%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title"><?php echo $this->lang->line('hrms_over_time_employee');?><!--Employees--></h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-7">
                        <div class="row">
                            <div class="form-group col-sm-6 col-xs-6 select-container">
                                <label for="otGroupID"> <?php echo $this->lang->line('common_group');?><!--Group--> </label>
                                <?php echo form_dropdown('', $otGroup_arr, '', 'class="form-control" id="otGroupID" multiple="multiple" onchange="openEmployeeModal()"'); ?>
                            </div>
                            <div class="form-group col-sm-6 col-xs-6 pull-right">
                                <button type="button" class="btn btn-primary btn-sm pull-right" onclick="selectAllRows()" >
                                    <?php echo $this->lang->line('common_select_all');?>   <!--Select All-->
                                </button>
                            </div>
                        </div>

                        <hr style="margin: 10px 0px 10px;" class="hidden-sm hidden-xs">
                        <div class="row">
                            <div class="table-responsive col-md-12">
                                <table id="emp_modalTB" class="<?php echo table_class(); ?>">
                                <thead>
                                <tr>
                                    <th style="min-width: 10px">#</th>
                                    <th style="min-width: 100px"><?php echo $this->lang->line('hrms_over_time_emp_code');?><!--EMP Code--></th>
                                    <th style="width:auto"><?php echo $this->lang->line('hrms_over_time_employee_name');?><!--Employee Name--></th>
                                    <th style="width:auto"><?php echo $this->lang->line('common_group');?><!--Group--></th>
                                    <th style="width: 40px"><div id="dataTableBtn"></div></th>
                                </tr>
                                </thead>
                            </table>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive col-md-5" >
                        <div class="pull-right">
                            <button class="btn btn-primary btn-sm" id="addAllBtn" style="font-size:12px;" onclick="addAllRows()"> <?php echo $this->lang->line('common_add_all');?><!--Add All--> </button>
                            <button class="btn btn-default btn-sm" id="clearAllBtn" style="font-size:12px;" onclick="clearAllRows()"> <?php echo $this->lang->line('common_clear_all');?><!--Clear All--> </button>
                        </div>
                        <hr style="margin-top: 7%">
                        <form id="tempTB_form">
                            <input type="hidden" name="masterID" id="masterID"/>
                            <input type="hidden" name="type_m" value="MA"/>
                            <table class="<?php echo table_class(); ?>" id="tempTB">
                                <thead>
                                <tr>
                                    <th style="max-width: 5%"><?php echo $this->lang->line('hrms_over_time_emp_code');?><!--EMP CODE--></th>
                                    <th style="max-width: 95%"><?php echo $this->lang->line('hrms_over_time_emp_name');?><!--EMP NAME--></th>
                                    <th><div id="removeBtnDiv"> </div></th>
                                </tr>
                                </thead>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button" style="font-size:12px;"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>



<script type="text/javascript">
    var empTempory_arr = [];
    var empTB = $('#empTB');
    var emp_modalTB = $('#emp_modalTB');
    var tempTB = $('#tempTB').DataTable({ "bPaginate": false });

    var segmentDrop = $('#otGroupID');

    segmentDrop.multiselect2({
        includeSelectAllOption: true,
        selectAllValue: 'select-all-value',
        maxHeight: 200,
        numberDisplayed: 1
    });

    segmentDrop.multiselect2('updateButtonText');

    $( document ).ready(function() {
        var documentID = '<?php echo $this->input->post('page_id');?>';

        if( documentID != 0){
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data : { 'editID': documentID},
                url: "<?php echo site_url('Employee/edit_OT_monthAddition'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#isConfirm').val(data['confirmedYN']);
                    $("#documentCode").val(data['monthlyAdditionsCode']);
                    $("#updateCode").val(data['monthlyAdditionsCode']);
                    $('#updateID').val( documentID );
                    $('#masterID').val( documentID );
                    $('#desDate').val(data['dateMA']);
                    $('#description').val(data['description']);
                    $('#currencyID').val(data['currencyID']);


                    if( data['confirmedYN'] == 1){
                        disableAllFields();
                        $('#removeAll_emp').hide();
                    }


                }, error: function () {
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                }
            });

        }

        $('.headerclose').click(function(){
            fetchPage('system/hrm/OverTimeManagementSalamAir/over_time_monthly_addition', documentID,'HRMS');
        });



        Inputmask().mask(document.querySelectorAll("input"));

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';


        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        });

        loadDetail_table();
    });

    $(document).on('keypress', '.number',function (event) {
        var amount = $(this).val();
        if(amount.indexOf('.') > -1) {
            if (event.which != 8 && isNaN(String.fromCharCode(event.which))) {
                event.preventDefault();
            }
        }
        else {
            if (event.which != 8 && event.which != 46 && isNaN(String.fromCharCode(event.which))) {
                event.preventDefault();
            }
        }

    });


    function formatAmount(obj, decimal=2) {
        id = obj.id;
        if ($('#' + id).val() == '') {
            $('#' + id).val(0);
        }
        var amount = $('#' + id).val().replace(/,/g, "");
        amount = parseFloat(amount).toFixed(decimal);
        $('#' + id).val(commaSeparateNumber(amount, decimal));

        saveInline(obj);
    }

    function openEmployeeModal(){
        var desDate = $('#desDate').val();
        if( desDate != '' ){
            $('#employee_model').modal({backdrop:'static'});
            emp_modalTB.DataTable().destroy();
            load_employeeForModal(desDate);
        }
        else{
            myAlert('e', 'Please select a date to load employee');
        }
    }

    function load_employeeForModal(desDate){
        $('#emp_modalTB').DataTable( {
            dom: 'Bfrtip',
            buttons: [
                'colvis',
                'excel',
                'print'
            ]
        } );

        var Otable = $('#emp_modalTB').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/getEmployeesDataTable_withLastWorkingDay_validation'); ?>?entryDate="+desDate,
            "aaSorting": [[1, 'asc']],
            "aLengthMenu": [[10, 25, 50, 100, 200], [10, 25, 50, 100, 200]],
            "pageLength": 200,
            "fnInitComplete": function () {
                /*$('#selectAllBtn').remove();
                 var addAll = '<button class="btn btn-primary btn-xs" id="selectAllBtn" style="font-size:12px; margin-left: 16%" onclick="selectAllRows()"> ADD ALL </button>';
                 $('#dataTableBtn').append(addAll);*/
            },
            "fnDrawCallback": function (oSettings) {

                var tmp_i   = oSettings._iDisplayStart;
                var iLen    = oSettings.aiDisplay.length;

                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }

            },
            "aoColumns": [
                {"mData": "EIdNo"},
                {"mData": "ECode"},
                {"mData": "empName"},
                {"mData": "otGroupDescription"},
                {"mData": "addBtn"},
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name':'currencyID', 'value': $('#currencyID').val()});
                aoData.push({'name':'otGroupID', 'value': $('#otGroupID').val()});
                aoData.push({'name':'isOT_addition', 'value': '1'});
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

    $('.submitBtn').click(function(){
        var isConfirm = $('#isConfirm');
        isConfirm.val( $(this).attr('data-value') );


        if( isConfirm.val() == 0 ){
            save();
        }
        else{
            var count = 0;
            $('.trInputs').each(function(){
                if( $.trim($(this).val()) == '' ){
                    count += 1;
                    //$(this).css('border-color', 'red');
                    $(this).addClass('required-class');
                }
            });

            if( count == 0) {
                getConformation();
            }else{
                myAlert('e', '<?php echo $this->lang->line('common_please_fill_all_required_fields');?>');/*Please fill all required fields*/
            }

        }

    });

    function save(){
        var data = $('#emp_monthlyAddFrm').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Employee/save_empMonthlyAdditionOT'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                if( data[0] == 's'){
                    var eID = '<?php echo $this->input->post('page_id'); ?>';
                    fetchPage('system/hrm/OverTimeManagementSalamAir/over_time_monthly_addition',eID,'HRMS');
                }
            }, error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function getConformation(){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes!*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                save();
            }
        );
    }

    $(document).on('keypress change','.trInputs', function(){
        $(this).removeClass('required-class');
        $(this).addClass('remove-required-class');
    });

    function addTempTB(det){

        var table = $('#emp_modalTB').DataTable();
        var thisRow = $(det);

        var details = table.row(  thisRow.parents('tr') ).data();
        var empID = details.EIdNo;


        var inArray = $.inArray(empID, empTempory_arr);
        if (inArray == -1) {

            var empDet = '<div class="pull-right"><input type="hidden" name="temp_empHiddenID[]"  class="modal_empID" value="'+details.EIdNo+'">';
            empDet += '<input type="hidden" name="temp_empCurrencyID[]" class="modal_CurrencyID" value="'+details.currencyID+'">';
            empDet += '<input type="hidden" name="temp_empCurrencyCode[]" class="modal_CurrencyCode" value="'+details.CurrencyCode+'">';
            empDet += '<input type="hidden" name="temp_empCurrencyDPlace[]" class="modal_dPlaces" value="'+details.DecimalPlaces+'">';
            empDet += '<input type="hidden" name="temp_rateInt[]"  class="modal_rateInt" value="'+details.rateInt+'">';
            empDet += '<input type="hidden" name="temp_rateIntLay[]"  class="modal_rateIntLay" value="'+details.rateIntLay+'">';
            empDet += '<input type="hidden" name="temp_rateLocalLay[]"  class="modal_rateLocalLay" value="'+details.rateLocalLay+'">';
            empDet += '<input type="hidden" name="temp_slabID[]"  class="modal_slabID" value="'+details.slabID+'">';
            empDet += '<span class="glyphicon glyphicon-trash" onclick="removeTempTB(this)" style="color:rgb(209, 91, 71);"></span> </a></div>';

            tempTB.rows.add([{
                0:  details.ECode,
                1:  details.empName,
                2:  empDet,
                3:  empID
            }]).draw();

            empTempory_arr.push(empID);
        }
    }

    function selectAllRows(){
        var tempTB = $('#tempTB').DataTable();
        var emp_modalTB = $('#emp_modalTB').DataTable();
        var empDet1;
        emp_modalTB.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
            var data = this.data();
            var empID = data.EIdNo;

            var inArray = $.inArray(empID, empTempory_arr);
            if (inArray == -1) {
                empDet1 = '<div class="pull-right"><input type="hidden" name="temp_empHiddenID[]" class="modal_empID" value="'+data.EIdNo+'">';
                empDet1 += '<input type="hidden" name="temp_empCurrencyID[]" class="modal_CurrencyID" value="'+data.currencyID+'">';
                empDet1 += '<input type="hidden" name="temp_empCurrencyCode[]" class="modal_CurrencyCode" value="'+data.CurrencyCode+'">';
                empDet1 += '<input type="hidden" name="temp_empCurrencyDPlace[]" class="modal_dPlaces" value="'+data.DecimalPlaces+'">';
                empDet1 += '<input type="hidden" name="temp_rateInt[]"  class="modal_rateInt" value="'+data.rateInt+'">';
                empDet1 += '<input type="hidden" name="temp_rateIntLay[]"  class="modal_rateIntLay" value="'+data.rateIntLay+'">';
                empDet1 += '<input type="hidden" name="temp_rateLocalLay[]"  class="modal_rateLocalLay" value="'+data.rateLocalLay+'">';
                empDet1 += '<input type="hidden" name="temp_slabID[]"  class="modal_slabID" value="'+data.slabID+'">';
                empDet1 += '<span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);" onclick="removeTempTB(this)"></span> </a></div>';

                tempTB.rows.add([{
                    0:  data.ECode,
                    1:  data.empName,
                    2:  empDet1,
                    3:  empID
                }]).draw();

                empTempory_arr.push(empID);
            }
        } );
    }

    function addAllRows(){
        var data = $('#emp_monthlyAddFrm').serializeArray();
        var postData = $('#tempTB_form').serializeArray();
        var allData = postData.concat(data);
        allData.push({'name':'selectedEmployees', 'value':empTempory_arr});

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: allData,
            url: "<?php echo site_url('Employee/save_OT_employeeAsTemp'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                if( data[0] == 'e'){
                    myAlert('e', data[1]);
                }else{
                    setTimeout(function(){
                        loadDetail_table();
                    }, 300);
                    $('#employee_model').modal('hide');
                    clearAllRows();
                }
            },
            error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });

    }

    function clearAllRows(){
        var table = $('#tempTB').DataTable();
        table.clear().draw();
        empTempory_arr = [];
    }

    function remove_emp_OT(det, detailID){

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
                    data: {'detailID':detailID, 'masterID':'<?php echo $this->input->post('page_id'); ?>'},
                    url: "<?php echo site_url('Employee/remove_emp_OT'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();

                        if( data[0] == 'e'){
                            myAlert('e', data[1]);
                        }else{
                            $(det).closest('tr').remove();
                            $('#searchItem').keyup();
                            $('#totalRowCount').text($('#details_table tbody>tr').length);
                            if( $('#details_table tbody>tr').length == 0){
                                $('#removeAll_emp').hide();
                            }
                        }
                    },
                    error: function () {
                        myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                        stopLoad();
                    }
                });

            }
        );
    }

    function removeTempTB(det){
        var table = $('#tempTB').DataTable();
        var thisRow = $(det);
        var details = table.row(  thisRow.parents('tr') ).data();
        empID = details[3];

        empTempory_arr = $.grep(empTempory_arr, function(data) {
            return parseInt(data) != empID
        });

        table.row( thisRow.parents('tr') ).remove().draw();
    }

    function disableAllFields(){
        $('#desDate').prop('disabled', true);
        $('#description').prop('disabled', true);
        $('.saveBtn').prop('disabled', true);
        $('.trInputs').prop('disabled', true);
    }

    function removeAllEmp_OT(){
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
                var updateID = $('#updateID').val();

                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'masterID' :updateID},
                    url: "<?php echo site_url('Employee/removeAllEmp_OT'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();

                        if( data[0] == 'e'){
                            myAlert('e', data[1]);
                        }else{
                            setTimeout(function(){
                                loadDetail_table();
                            }, 300);
                        }
                    },
                    error: function () {
                        myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                        stopLoad();
                    }
                });
            }
        );
    }

    function loadDetail_table(){
        var documentID = '<?php echo $this->input->post('page_id');?>';
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'masterID' :documentID},
            url: "<?php echo site_url('Employee/loadOTDetail_table'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#detail-table-container').html(data);
            },
            error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }
</script>

<?php
