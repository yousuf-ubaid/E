<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('hrms_leave_management_lang', $primaryLanguage);
$this->lang->load('hrms_payroll', $primaryLanguage);

$date_format_policy = date_format_policy();
$masterID = $this->input->post('page_id');
$segment_arr = fetch_segment(true, false);
$sal_cat = salary_categories(['A', 'D'], 1);
$leave_arr = load_leave_type_drop();

$title = $this->lang->line('hrms_leave_management_leave_encashment');
echo head_page('Project Transfer', false);

$encash_policy = getPolicyValues('LEB', 'All'); //Leave encashment policy
$no_of_working_days = 22;
$readonly = '';
if($encash_policy == 1){
    $salaryProportionFormulaDays = getPolicyValues('SPF', 'All'); // Salary Proportion Formula
    $no_of_working_days = ($salaryProportionFormulaDays == 365)? 30.42: 30;
    $readonly = 'readonly';
}
?>
    <style>
        fieldset {
            border: 1px solid silver;
            border-radius: 0px;
            padding-bottom: 15px;
            margin: 10px;
        }

        legend {
            width: auto;
            border-bottom: none;
            margin: 0px 10px;
            font-size: 20px;
            font-weight: 500
        }

        .row-centered {
            text-align:center;
        }

        .col-centered {
            display:inline-block;
            float:none;
            /* reset the text-align */
            text-align:left;
            /* inline-block space fix */
            margin-right:-4px;
            text-align: center;
        }
    </style>


    <div id="response-container"> </div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

    <div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel"
         id="project_transfer_modal_emp">
        <div class="modal-dialog" style="width: 50%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="usergroup-title">Transfer Employee</h4>
                </div>

                <div class="modal-body">

                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-3 col-md-offset-1">
                            <label class="title">Transfer Employee</label>
                        </div>
                        <div class="form-group col-sm-6">
                            <div id="div_load_transferemployees">
                                <select name="employees" class="form-control" id="filter_employees"
                                        multiple="">
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-3 col-md-offset-1">
                            <label class="title">Transfer Project</label>
                        </div>
                        <div class="form-group col-sm-4">
                     <span class="input-req" title="Required Field">
                             <div id="div_load_transferprojectID">
                              <select name="transferprojectID" class="form-control select2" id="transferprojectID">
                              </select>
                            </div>

                         </select>
                     </span>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-3 col-md-offset-1">
                            <label class="title">Transfer Task</label>
                        </div>
                        <div class="form-group col-sm-4">
                          <span class="input-req" title="Required Field">
                                     <div id="div_load_transfertask">
                                      <select name="transfertaskID" class="form-control select2" id="transfertaskID">
                                      </select>
                                    </div>

                              </select>
                             </span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" onclick="submit_project_transfer()" class="btn btn-sm btn-primary"><span class="glyphicon glyphicon-floppy-disk"
                                                                                                                       aria-hidden="true"></span> Save
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>


    <script type="text/javascript">
        var startdatenonformated = '';
        var headerid = '';
        var empid = '';
        var projectplanningID = '';
        var enddatenonformated = '';
        var en_masterID  = <?=$masterID?>;
        var emp_modalTB = $('#emp_modalTB');
        var empTemporary_arr = [];
        var tempTB = $('#tempTB').DataTable({
            "bPaginate": false,
            "columnDefs": [ {
                "targets": [2],
                "orderable": false
            } ]
        });
        var disableDate = '';
        var error_occurred_str = '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.'; /*An Error Occurred! Please Try Again*/

        $('#leave_type').select2();
        $('.number').numeric({negative: false});

        $('#calculate_based_on').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

        $('#segmentID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });


        $(document).ready(function () {


            $('.headerclose').click(function(){
                fetchPage('system/pm/project_transfer','','PM');
            });

            load_detail_view();
        });



        function max_month_days(){
            var obj = $('#no_of_working_days');

            if( parseInt(obj.val()) > 31 ){
                obj.val('');
                myAlert('w', 'Maximum days can not be greater than 31')
            }
        }

        function delete_item(detailID) {
            swal({
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
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'detailID': detailID, 'masterID': en_masterID},
                        url: "<?php echo site_url('Employee/delete_leave_encashment_item'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if(data[0] == 's'){
                                load_detail_view();
                            }
                        }, error: function () {
                            stopLoad();
                            myAlert('e', error_occurred_str);
                        }
                    });
                });
        }

        function delete_all_item() {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                    text: "<?php echo $this->lang->line('common_you_want_to_delete_all');?>",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55 ",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'masterID': en_masterID},
                        url: "<?php echo site_url('Employee/delete_all_leave_encashment_details'); ?>",
                        beforeSend: function () {
                            startLoad();
                        }, success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if(data[0] == 's'){
                                load_detail_view();
                            }
                        }, error: function () {
                            stopLoad();
                            myAlert('e', error_occurred_str);
                        }
                    });
                });
        }

        function open_details_modal(){
            $("#calculate_based_on").multiselect2("deselectAll", false);
            $("#calculate_based_on").multiselect2("refresh");

            $('#bulkDetails_modal').modal('show');

            emp_modalTB.DataTable().destroy();
            var bulk_effectiveDate = $('#doc_data').val();
            load_employeeForBulk(bulk_effectiveDate);
        }

        function load_employeeForBulk_refresh(){
            var bulk_effectiveDate = $('#doc_data').val();
            load_employeeForBulk(bulk_effectiveDate);
        }

        function load_employeeForBulk(effectiveDate=null){
            emp_modalTB.DataTable({
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "StateSave": true,
                "sAjaxSource": "<?php echo site_url('Employee/getEmployeesDataTable_salaryDeclaration'); ?>?entryDate="+effectiveDate,
                "aaSorting": [[1, 'asc']],
                "fnInitComplete": function () {

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
                    {"mData": "segTBCode"},
                    {"mData": "CurrencyCode"},
                    {"mData": "addBtn"}
                ],
                "fnServerData": function (sSource, aoData, fnCallback) {
                    aoData.push({'name':'isNonPayroll', 'value':$('#payrollType').val()});
                    aoData.push({'name':'segmentID', 'value':$('#segmentID').val()});
                    aoData.push({'name':'currencyFilter', 'value':$('#docCurrency').val()});
                    aoData.push({'name':'isFromSalaryDeclaration', 'value':'1'});

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

        function addTempTB(det){

            var table = $('#emp_modalTB').DataTable();
            var thisRow = $(det);

            var details = table.row(  thisRow.parents('tr') ).data();
            var empID = details.EIdNo;

            var inArray = $.inArray(empID, empTemporary_arr);
            if (inArray == -1) {
                var empDet = '<div class="pull-right"><input type="hidden" name="temp_empHiddenID[]"  class="modal_empID" value="' + details.EIdNo + '">';
                empDet += '<input type="hidden" name="temp_accGroupID[]" class="modal_accGroupID" value="' + details.accGroupID + '">';
                empDet += '<span class="glyphicon glyphicon-trash" onclick="removeTempTB(this)" style="color:rgb(209, 91, 71);"></span> </a></div>';

                tempTB.rows.add([{
                    0: details.ECode,
                    1: details.empName,
                    2: empDet,
                    3: empID
                }]).draw();
                empTemporary_arr.push(empID);
            }
        }

        function selectAllRows(){
            var tempTB = $('#tempTB').DataTable();
            var emp_modalTB = $('#emp_modalTB').DataTable();
            var empDet1;
            emp_modalTB.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
                var data = this.data();
                var empID = data.EIdNo;

                var inArray = $.inArray(empID, empTemporary_arr);

                if (inArray == -1) {
                    empDet1 = '<div class="pull-right"><input type="hidden" name="temp_empHiddenID[]" class="modal_empID" value="' + data.EIdNo + '">';
                    empDet1 += '<input type="hidden" name="temp_accGroupID[]" class="modal_accGroupID" value="' + data.accGroupID + '">';
                    empDet1 += '<span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);" onclick="removeTempTB(this)"></span> </a></div>';

                    tempTB.rows.add([{
                        0: data.ECode,
                        1: data.empName,
                        2: empDet1,
                        3: empID
                    }]).draw();

                    empTemporary_arr.push(empID);
                }
            } );
        }

        function removeTempTB(det){
            var table = $('#tempTB').DataTable();
            var thisRow = $(det);
            var details = table.row(  thisRow.parents('tr') ).data();
            empID = details[3];

            empTemporary_arr = $.grep(empTemporary_arr, function(data) {
                return parseInt(data) != empID
            });

            table.row( thisRow.parents('tr') ).remove().draw();
        }

        function clearAllRows(){
            var table = $('#tempTB').DataTable();
            empTemporary_arr = [];
            table.clear().draw();
        }

        function addAllRows(){
            var postData = $('#bulk_form').serializeArray();

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: postData,
                url: "<?php echo site_url('Employee/add_leave_encashment_employees'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();

                    if( data[0] == 'e'){
                        myAlert('e', data[1]);
                    }
                    else if( data[0] == 'm'){
                        bootbox.alert('<div class="alert alert-danger"><strong><i class="fa fa-exclamation-triangle fa-2x"></i> Error! </strong><br/>'+data[1]+'</div>');
                    }else{
                        $('#bulkDetails_modal').modal('hide');
                        setTimeout(function(){
                            load_detail_view();
                        }, 300);

                        clearAllRows();
                    }
                },
                error: function () {
                    myAlert('e', error_occurred_str);
                    stopLoad();
                }
            });

        }


        function project_transfer(planningID,startdate,enddate,employeeID,headerID)
        {
            $('#div_load_transferprojectID').html('<select name="transfertaskID" class="form-control select2" id="transfertaskID"><option value=" ">Select Transfer Project</option></select>');
            $('#div_load_transfertask').html('<select name="transfertaskID" class="form-control select2" id="transfertaskID"><option value=" ">Select Transfer Task</option></select>');
            load_project_employees(planningID,employeeID);
            load_project_transfer(headerID);
            startdatenonformated = startdate;
            enddatenonformated =enddate;
            headerid = headerID;
            empid = employeeID;
            projectplanningID = planningID;

            $('#project_transfer_modal_emp').modal('show');
        }
        function load_project_employees(planningID,employeeID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {planningID: planningID,employeeID,employeeID},
                url: "<?php echo site_url('Boq/fetch_employee_drop_by_task'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#div_load_transferemployees').html(data);
                    $('#filter_employees').multiselect2({
                        enableCaseInsensitiveFiltering: true,
                        includeSelectAllOption: true,
                        selectAllValue: 'select-all-value',
                        //enableFiltering: true
                        buttonWidth: 150,
                        maxHeight: 200,
                        numberDisplayed: 1
                    });
                    $("#filter_employees").multiselect2('selectAll', false);
                    $("#filter_employees").multiselect2('updateButtonText');
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }
        function load_project_transfer(headerID)
        {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {headerID: headerID},
                url: "<?php echo site_url('Boq/fetch_employee_project'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {

                    $('#div_load_transferprojectID').html(data);

                    $('.select2').select2();
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }
        function fetch_transfertask(transfertaskID) {
            if(transfertaskID!='')
            {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'html',
                    data: {transfertaskID: transfertaskID,startdate:startdatenonformated,enddate:enddatenonformated,projectplanningID:projectplanningID},
                    url: "<?php echo site_url('Boq/fetch_transfertaskid'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        $('#div_load_transfertask').html(data);
                        $('.select2').select2();
                        stopLoad();
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        myAlert('e', '<br>Message: ' + errorThrown);
                    }
                });
            }else
            {
                $('#div_load_transfertask').html('<select name="transfertaskID" class="form-control select2" id="transfertaskID"><option value=" ">Select Transfer Task</option></select>');
            }

        }
        function submit_project_transfer() {
            var filter_employees = $('#filter_employees').val();
            var transferprojectID = $('#transferprojectID').val();
            var transfertaskID = $('#transfertaskID').val();
            var transfermasterID = $('#transfermasterID').val();

            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "You want to transfer this employee",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    cancelButtonText: "No"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'headerid': headerid,'filter_employees':filter_employees,'transferprojectID':transferprojectID,'transfertaskID':transfertaskID,projectplanningID:projectplanningID,transfermasterID:transfermasterID},
                        url: "<?php echo site_url('Boq/save_project_transfer'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                oTable.draw();


                                stopLoad();
                                $('#project_transfer_modal_emp').modal('hide');
                            }
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });

        }
        function load_detail_view(){
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'masterID': '<?=$masterID?>'},
                url: "<?php echo site_url('Boq/fetch_emp_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                    $('#sal-dec-body').html('');
                },
                success: function (data) {
                    stopLoad();
                    if (data[0] == 's') {
                        $('#response-container').html( data['view'] );
                    }
                    else{
                        myAlert(data[0], data[1]);
                    }
                }, error: function () {
                    myAlert('e', 'Some thing went gone wrong.<p>Please try again.')
                }
            });

        }
    </script>


<?php
