<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('employee_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
 echo head_page('', true);/**SMSD */

if ($isInitialLoad == 'Y') {
    ?>
    <div class="row"> <!--close tag for this is in load_empDepartmentView.php-->
    <div id="designation-container" class="col-md-7">
<?php } ?>

    <style type="text/css">
        .items {
            margin: 0px !important;
        }
    </style>

    <fieldset>
        <legend><?php echo $this->lang->line('emp_designation'); ?><!--Designation--></legend>
        <form method="POST" id="frm_employeeDesignationPdf" class="form-horizontal" action="" name="frm_employeeDesignationPdf">
            <input type="hidden" name="empID" value="<?php echo $empID ?>">
        </form>
        <div class="row" style="margin-top: -20px;">
            <div class="col-md-5">&nbsp;</div>
            <div class="col-md-7 pull-right" style="margin-right: 15px;">
                <button type="button" class="btn btn-primary-new size-sm pull-right"
                        onclick="openEmpDesignation_modal()"><i
                        class="fa fa-plus-square"></i>&nbsp; <?php echo $this->lang->line('emp_add'); ?> <!--Add-->
                </button>
                &nbsp
                <button class="btn btn-pdf btn-danger-new size-sm pull-right" id="btn-pdf" type="button" onclick="generateEmployeeDesignationPdf()" style="margin-right: 1%;">
                    <i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF
                </button>

            </div>
        </div>

        <div class="table-responsive" style="margin-top: 1%;">
            <table id="load_empDesignations" class="<?php echo table_class(); ?>">
                <thead>
                <tr>
                    <th style="min-width: 5%">#</th>
                    <th style="width: auto"><?php echo $this->lang->line('emp_designation'); ?><!--Designation--></th>
                    <th style="width: 70px"><?php echo $this->lang->line('emp_start_date'); ?><!--Start Date--></th>
                    <th style="width: 70px"><?php echo $this->lang->line('emp_end_date'); ?><!--End Date--></th>
                    <th style="width: 50px"><?php echo $this->lang->line('emp_is_primary'); ?><!--Is Primary--></th>
                    <th style="width: 50px"><?php echo $this->lang->line('emp_is_active'); ?><!--Is Active--></th>
                    <th style="width: 60px"></th>
                </tr>
                </thead>
            </table>
        </div>
    </fieldset>

    <div class="modal fade" id="new_empDesignation" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">
                        <?php echo $this->lang->line('emp_add_designations'); ?><!--Add Designations-->
                    </h4>
                </div>
                <form class="form-horizontal" id="add-empDesignation_form">
                    <div class="modal-body">

                        <table class="table table-bordered" id="designations-tb">
                            <thead>
                            <tr>
                                <th><?php echo $this->lang->line('emp_designation'); ?><!--Designations--></th>
                                <th><?php echo $this->lang->line('emp_start_date'); ?><!--Start Date--></th>
                                <th><?php echo $this->lang->line('emp_end_date'); ?><!--End Date--></th>
                                <th><?php echo $this->lang->line('emp_is_major'); ?><!--Is Primary--></th>
                            </tr>
                            </thead>

                            <tbody>
                            <?php
                            $date_format_policy = date_format_policy();

                            $option = '<select name="designationID" id="designationID" class="form-control select2">';
                            $option .= '<option value="">Select a designation</option>';
                            foreach ($moreDesignation as $key => $item) {
                                $option .= '<option value="' . $item['DesignationID'] . '">' . $item['DesDescription'] . '</option>';
                            }
                            $option .= '</select>';
                            echo '<tr>
                                    <td>' . $option . '</td>
                                    <td style="width:140px">
                                        <div class="input-group datepic">
                                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                            <input type="text" name="startDate" data-inputmask="\'alias\': \'' . $date_format_policy . '\'"
                                                value="" id="startDate_new" class="form-control startDate" required>
                                        </div>
                                    </td>
                                    <td style="width:140px">
                                        <div class="input-group datepic">
                                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                            <input type="text" name="endDate" data-inputmask="\'alias\': \'' . $date_format_policy . '\'"
                                                value="" id="" class="form-control endDate" required>
                                        </div>
                                    </td>
                                    <td align="center">
                                        <input type="checkbox" name="isMajor" class="isMajor" value="1" />
                                    </td>
                                 </tr>';
                            ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="empID" value="<?php echo $empID ?>">
                        <button type="button" class="btn btn-primary btn-sm" onclick="save_empDesignations()">
                            <?php echo $this->lang->line('emp_save'); ?> <!--Save-->
                        </button>
                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                            <?php echo $this->lang->line('emp_Close'); ?><!--Close-->
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="edit_empDesignation" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('emp_edit_designations'); ?> </h4><!--Edit Designations-->
                </div>
                <form class="form-horizontal" id="edit-empDesignation_form">
                    <div class="modal-body">

                        <table class="table table-bordered" id="edit-designations-tb">
                            <thead>
                            <tr>
                                <th><?php echo $this->lang->line('emp_designation'); ?><!--Designations--></th>
                                <th><?php echo $this->lang->line('emp_start_date'); ?><!--Start Date--></th>
                                <th><?php echo $this->lang->line('emp_end_date'); ?><!--End Date--></th>

                            </tr>
                            </thead>

                            <tbody>
                            <tr>
                                <td><input type="text" class="form-control" id="edit_designationDescription" disabled>
                                </td>
                                <td style="width:140px">
                                    <div class="input-group datepic">
                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        <input type="text" name="edit_startDate"
                                               data-inputmask="'alias': '<?php echo $date_format_policy; ?>'"
                                               value="" id="edit_startDate" class="form-control" required>
                                    </div>
                                </td>
                                <td style="width:140px">
                                    <div class="input-group datepic">
                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        <input type="text" name="edit_endDate"
                                               data-inputmask="'alias': '<?php echo $date_format_policy; ?>'"
                                               value="" id="edit_endDate" class="form-control" required>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="empID" value="<?php echo $empID ?>">
                        <input type="hidden" name="designationID-hidden" id="designationID-hidden" value="">
                        <button type="button" class="btn btn-primary btn-sm" onclick="edit_empDesignations()"><?php echo $this->lang->line('common_update'); ?>
                        </button><!--Update-->
                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript">

        var empID = '<?php echo $empID; ?>';
        $('.select2').select2();
        Inputmask().mask(document.querySelectorAll("input"));

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy
        });

        $(document).ready(function () {
            load_empDesignations();
        });


        function load_empDesignations() {
            var Otable = $('#load_empDesignations').DataTable({
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "StateSave": true,
                "sAjaxSource": "<?php echo site_url('Employee/fetch_empDesignations'); ?>",
                "aaSorting": [[0, 'desc']],
                "aoColumnDefs": [{"bSortable": false, "aTargets": [4, 5, 6]}],
                "fnInitComplete": function () {

                },
                "fnDrawCallback": function (oSettings) {
                    if (oSettings.bSorted || oSettings.bFiltered) {
                        for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                            $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                        }
                    }
                    $(".switch-chk").bootstrapSwitch();
                    if(fromHiarachy==1){
                        Otable.column( 6 ).visible( false );
                        //$(".switch-chk").attr('disabled',true);
                        $(".switch-chk").bootstrapSwitch("disabled",true);
                    }
                },
                "aoColumns": [
                    {"mData": "DesignationID"},
                    {"mData": "DesDescription"},
                    {"mData": "startDate_format"},
                    {"mData": "endDate_format"},
                    {"mData": "isMajorAction"},
                    {"mData": "isActiveAction"},
                    {"mData": "edit"}
                ],
                "fnServerData": function (sSource, aoData, fnCallback) {
                    aoData.push({'name': 'empID', 'value': empID});
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

        function openEmpDesignation_modal() {
            $('#add-empDesignation_form')[0].reset();
            $('#designationID').change();
            $('#new_empDesignation').modal({backdrop: "static"});
            var empDesignationCount = '<?php echo $empDesignationCount; ?>';
            if (empDesignationCount < 1) {
                getStartDate();
            }
        }

        function getStartDate() {
            $.ajax({
                type: 'post',
                url: '<?php echo site_url('Employee/getEmployeeJoinDate'); ?>',
                data: {'empID': empID},
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    if (data[0] == 's') {
                        setTimeout(function () {
                            $('#startDate_new').val(data[1]);
                        }, 300);
                    }

                },
                error: function () {
                    stopLoad();
                    myAlert('e', 'An Error Occurred!');
                }
            })
        }

        function save_empDesignations() {
            var postData = $('#add-empDesignation_form').serializeArray();

            $.ajax({
                type: 'post',
                url: '<?php echo site_url('Employee/save_empDesignations'); ?>',
                data: postData,
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if (data[0] == 's') {
                        $('#new_empDesignation').modal('hide');

                        setTimeout(function () {
                            fetch_designation();
                        }, 400);
                    }

                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                }
            })
        }

        function edit_empDesignation(obj) {
            var table = $('#load_empDesignations').DataTable();
            var thisRow = $(obj);
            var details = table.row(thisRow.parents('tr')).data();


            $('#edit_startDate').val(details.startDate_format);
            $('#edit_endDate').val(details.endDate_format);
            $('#edit_designationDescription').val(details.DesDescription);
            $('#designationID-hidden').val(details.EmpDesignationID);

            $('#edit_empDesignation').modal({backdrop: "static"});
        }

        function edit_empDesignations() {
            var postData = $('#edit-empDesignation_form').serializeArray();
            $.ajax({
                type: 'post',
                url: '<?php echo site_url('Employee/edit_empDesignations'); ?>',
                data: postData,
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if (data[0] == 's') {
                        $('#edit_empDesignation').modal('hide');

                        setTimeout(function () {
                            fetch_designation();
                        }, 400);
                    }

                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                }
            })
        }

        function delete_empDesignation(id, description) {
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
                        async: true,
                        url: "<?php echo site_url('Employee/delete_empDesignation'); ?>",
                        type: 'post',
                        dataType: 'json',
                        data: {'hidden-id': id},
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                setTimeout(function () {
                                    fetch_designation();
                                }, 400);
                            }
                        }, error: function () {
                            stopLoad();
                            myAlert('e', 'error');
                        }
                    });
                }
            );
        }

        function changeDesignationStatus(obj, id) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_are_you_sure_you_want_to_make_this_as_p_d');?>",/*You want to make this record as primary designation!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function (isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            async: true,
                            url: "<?php echo site_url('Employee/changeEmpMajorDesignation'); ?>",
                            type: 'post',
                            dataType: 'json',
                            data: {'hidden-id': id, 'empID': empID},
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
                                stopLoad();
                                myAlert(data[0], data[1]);
                                if (data[0] != 's') {
                                    var thisChk = $('#designation_status' + id);
                                    var changeStatus = ( $(obj).prop('checked') ) ? false : true;
                                    var changeFn = thisChk.attr('onchange');

                                    thisChk.removeAttr('onchange');
                                    thisChk.prop('checked', changeStatus).change().attr('onchange', changeFn);
                                }
                                else{
                                    fetch_designation();
                                    }
                            }, error: function () {
                                stopLoad();
                                myAlert('e', 'error');
                            }
                        });
                    }
                    else {
                        var changeStatus = ( $(obj).prop('checked') ) ? false : true;
                        $('#designation_status' + id).prop('checked', changeStatus).change();
                    }
                }
            );
        }

        function changeActiveStatus(obj, id) {
            var msg, postStatus;
            if ($(obj).prop('checked')) {
                msg = 'activate';
                postStatus = 1;
            } else {
                msg = 'inactivate';
                postStatus = 0;
            }

            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_are_you_sure_you_want_to');?> " + msg + " <?php echo $this->lang->line('common_designation_simple');?>",/*You want to*/ /*designation!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function (isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            async: true,
                            url: "<?php echo site_url('Employee/changeActiveDesignation'); ?>",
                            type: 'post',
                            dataType: 'json',
                            data: {'hidden-id': id, 'empID': empID, 'status': postStatus},
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
                                stopLoad();
                                myAlert(data[0], data[1]);
                                if (data[0] != 's') {

                                    var thisChk = $('#designationActive_status' + id);
                                    var changeStatus = ( $(obj).prop('checked') ) ? false : true;
                                    var changeFn = thisChk.attr('onchange');

                                    thisChk.removeAttr('onchange');
                                    thisChk.prop('checked', changeStatus).change().attr('onchange', changeFn);

                                }
                            }, error: function () {
                                stopLoad();
                                myAlert('e', 'error');

                                var thisChk = $('#designationActive_status' + id);
                                var changeStatus = ( $(obj).prop('checked') ) ? false : true;
                                var changeFn = thisChk.attr('onchange');

                                thisChk.removeAttr('onchange');
                                thisChk.prop('checked', changeStatus).change().attr('onchange', changeFn);

                            }
                        });
                    }
                    else {
                        var changeStatus = ( $(obj).prop('checked') ) ? false : true;
                        $('#designationActive_status' + id).prop('checked', changeStatus).change();
                    }
                }
            );
        }

        function generateEmployeeDesignationPdf() {
            var form= document.getElementById('frm_employeeDesignationPdf');
            form.target='_blank';
            form.action='<?php echo site_url('Employee/load_empDesignation_PDF_print'); ?>';
            form.submit();
        }
    </script>

    <div class="clearfix">&nbsp;</div>

<?php if ($isInitialLoad == 'Y') {
    echo '</div>';
} ?>

<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-11-06
 * Time: 12:58 PM
 */