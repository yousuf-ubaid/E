<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('employee_master', $primaryLanguage);
$this->lang->load('hrms_payroll', $primaryLanguage);
$this->lang->load('hrms_leave_management_lang', $primaryLanguage);

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


    <div class="row well" style="padding: 10px;">
        <div class="col-md-2">
            <table class="table table-bordered table-condensed" style="background-color: #bed4ea; font-weight: bold">
                <tr>
                    <td style="width: 150px;"><?php echo $this->lang->line('common_date');?></td>
                    <td class="bgWhite details-td" id="" width="200px">
                        <?php echo $detail['Transferdate']; ?>
                    </td>
                </tr>
            </table>
        </div>

        <div class="col-md-5">
            <table class="table table-bordered table-condensed" style="font-weight: bold">
                <tr style="background-color: #bed4ea; font-weight: bold">
                    <td style="width: 150px;"><?php echo $this->lang->line('common_narration');?></td>
                    <td class="bgWhite details-td" id="" width="200px">
                        <?php echo $detail['Narration']; ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <?php

                echo '<button type="button" class="btn btn-primary btn-sm pull-right" onclick="open_project_transfer()" style="margin-right: 10px; margin-bottom: 10px;">
                      <i class="fa fa-plus"></i> Project
                  </button>';
            ?>
        </div>
        <div class="col-sm-12">
            <div class="table-responsive col-md-12">
                <input type="hidden" id="masterID" name="masterID" value="<?php echo $masterID?>">
                <table id="pm_modal_transfer" class="<?php echo table_class(); ?>">
                    <thead>
                    <tr>
                        <th style="min-width: 5%">#</th>
                        <th style="width: auto">Transfer Task From</th>
                        <th style="width:auto">Transfer Task To</th>
                        <th style="width:auto">Project From</th>
                        <th style="width:auto">Project To</th>
                        <th style="width:auto">Transferred Employee</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>

        <!--<div class="col-sm-12">
            <div class="table-responsive">
                <table class="<?php /*echo table_class() */?> drill-table" >
                    <thead>
                    <tr>
                        <th> # </th>
                        <th style="">Transfer Task From</th>
                        <th style=""> Transfer Task To</th>
                        <th style="width: 105px">Project From</th>
                        <th style="width: 105px"> Project To</th>
                        <th style="width: 105px">Transferred Employee</th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php
/*                    $i = 1;
                    if(!empty($detail_rec)){
                        foreach ($detail_rec as $row){
                            echo '<tr>
                                <td style="text-align: right">'.$i.'</td>                                                                                                   
                                <td >'.$row['projectplannig'].'</td>
                                <td >'.$row['transferplanning'].'</td>
                                <td>'.$row['projecttransferfrom'].'</td>
                                <td>'.$row['transferprojectname'].'</td>
                                <td>'.$row['Ename2'].'</td>';

                            $i ++;
                        }



                    }
                    else{
                        $no_record_found = $this->lang->line('common_no_records_found');
                        echo '<tr><td colspan="11" align="center">'.$no_record_found.'</td></tr>';
                    }
                    */?>
                    </tbody>
                </table>
            </div>
        </div>-->
    </div>


    <div class="modal fade" id="project_transfer_modal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static">
        <div class="modal-dialog" style="width: 95%" role="document">
            <div class="modal-content">
                <form class="" id="bulk_form" autocomplete="off">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="myModalLabel">Project Transfer</h4>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="transfermasterID" id="transfermasterID" value="<?php echo $masterID?>">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <input type="hidden" id="isEmpLoad" value="0" >
                                    <div class="col-sm-2 col-xs-4 select-container">
                                        <label for="segment">Project </label>
                                    </div>
                                    <div class="col-sm-4 col-xs-4 select-container">
                                        <?php echo form_dropdown('project[]',load_all_project(), '', 'class="form-control" id="projectID"  multiple="multiple"'); ?>
                                    </div>
                                    <div class="col-sm-4 col-xs-4 pull-right" style="/*padding-top: 24px;*/">
                                        <button type="button" onclick="load_pm_table()" class="btn btn-primary btn-sm pull-right" style="margin-right:10px">
                                            <?php echo $this->lang->line('common_load');?><!--Load-->
                                        </button>
                                    </div>
                                </div>

                                <hr style="margin: 10px 0px 10px;" class="hidden-sm hidden-xs">
                                <div class="row">
                                    <div class="table-responsive col-md-12">
                                        <table id="pm_modalTB" class="<?php echo table_class(); ?>">
                                            <thead>
                                            <tr>
                                                <th style="min-width: 5%">#</th>
                                                <th style="width: auto">Project Code</th>
                                                <th style="width:auto">Project Name</th>
                                                <th style="width:auto">Task</th>
                                                <th style="width:auto">Start Date</th>
                                                <th style="width:auto">End Date</th>
                                                <th style="width:auto">Assigned Employee</th>
                                                <th style="width: 5%"><div id="dataTableBtn"></div></th>
                                            </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">

                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
    <script>
        var startdatenonformated = '';
        var headerid = '';
        var empid = '';
        var projectplanningID = '';
        var enddatenonformated = '';
        var oTable;
        var oTable2;
        $(document).ready(function () {
            load_pr_transfer_table();
            $('.select2').select2();
            $('.headerclose').click(function () {
                fetchPage('system/pm/project_transfer', 'Test', '');
            });
            $('#projectID').multiselect2({
                enableCaseInsensitiveFiltering: true,
                includeSelectAllOption: true,
                numberDisplayed: 1,
                buttonWidth: '180px',
                maxHeight: '30px'
            });
        });
        function open_project_transfer() {
            load_pm_table()
            $('#project_transfer_modal').modal('show');
        }
        function load_pm_table(){
            oTable = $('#pm_modalTB').DataTable({
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "StateSave": true,
                "sAjaxSource": "<?php echo site_url('Boq/get_employee_tansferpm'); ?>",
                "aaSorting": [[1, 'asc']],
                "fnInitComplete": function () {

                },
                "fnDrawCallback": function (oSettings) {
                    $("[rel=tooltip]").tooltip();
                    var tmp_i   = oSettings._iDisplayStart;
                    var iLen    = oSettings.aiDisplay.length;

                    var x = 0;
                    for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                        x++;
                    }
                },
                "aoColumns": [
                    {"mData": "projectPlannningID"},
                    {"mData": "projectCode"},
                    {"mData": "project"},
                    {"mData": "description"},
                    {"mData": "startDate"},
                    {"mData": "endDate"},
                    {"mData": "ename2"},
                    {"mData": "transferbtn"}
                ],
                "columnDefs": [{"targets": [7], "orderable": false},{"targets": [0,6], "visible": true,"searchable": false}],
                "fnServerData": function (sSource, aoData, fnCallback) {
                    aoData.push({'name':'project', 'value':$('#projectID').val()});

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
        function load_pr_transfer_table(){
            oTable2 = $('#pm_modal_transfer').DataTable({
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "StateSave": true,
                "sAjaxSource": "<?php echo site_url('Boq/fetch_pt_master'); ?>",
                "aaSorting": [[1, 'asc']],
                "fnInitComplete": function () {

                },
                "fnDrawCallback": function (oSettings) {
                    $("[rel=tooltip]").tooltip();
                    var tmp_i   = oSettings._iDisplayStart;
                    var iLen    = oSettings.aiDisplay.length;

                    var x = 0;
                    for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                        x++;
                    }
                },
                "aoColumns": [
                    {"mData": "projectPlannningID"},
                    {"mData": "transferplanning"},
                    {"mData": "projectplannig"},
                    {"mData": "transferprojectname"},
                    {"mData": "projecttransferfrom"},
                    {"mData": "Ename2"},
                ],
                /*"columnDefs": [{"targets": [7], "orderable": false},{"targets": [0,6], "visible": true,"searchable": false}],*/
                "fnServerData": function (sSource, aoData, fnCallback) {
                    aoData.push({'name':'masterID', 'value':$('#masterID').val()});

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
                                oTable2.draw();

                                stopLoad();
                                $('#project_transfer_modal_emp').modal('hide');
                            }
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });

        }
    </script>
<?php
