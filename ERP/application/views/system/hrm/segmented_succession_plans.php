<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('employee_master', $primaryLanguage);
$this->lang->load('hrms_final_settlement_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_segmented_succession_plans_title');
echo head_page($title, false);


?>
<style>
    .error-message {
        color: red;
    }

    td {
        text-align: center;
    }

    .succession_plan_link {
        cursor: pointer;
        text-decoration: underline;
        color: #0000EE;
    }

    .action-button {
        display: inline-block;
        margin: 0 5px;
    }

    .multiselect2 {
        width: 262px;
    }

    .form-label {
        text-align: right;
    }
</style>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-10">

    </div>
    <div class="col-md-2 text-center">
        <input type="button" class="btn btn-primary" value="Add New" onclick="load_add_plan_dialog()"/>
    </div>

</div>
<hr>
<div class="container-fluid">
    <div class="row">
        <div class="table-responsive">
            <table id="succession_plan_table" class="<?php echo table_class() ?>">
                <thead>
                <tr>
                    <th style="">#</th>
                    <th style="">Document Code</th>
                    <th style="">Segment</th>
                    <th style="">Year</th>
                    <th style="">Employee Name</th>
                    <th style="">Designation</th>
                    <th style="">Created Date</th>
                    <th style="">Created User</th>
                    <th style="">Status</th>
                    <th style="width:15%"></th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>

<div class="modal fade" id="add_plan_dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width:75%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="houseEnr_title">Succession Plan Header</h4>
            </div>

            <div class="modal-body">
                <div class="container-fluid">

                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-2 form-label">
                            <label>Segment</label>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <input class="form-control" type="text" id="segment" disabled/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-2 form-label">
                            <label>Year <span style="color: red;">*</span></label>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <input class="form-control" type="text" id="segment_year"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-2 form-label"><label>Designation <span style="color: red;">*</span></label>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <select class="form-control" id="designation_dropdown"
                                        onchange="designation_onchange()">

                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-2 form-label"><label>Employee <span style="color: red;">*</span></label>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <div id="employee_dropdown_div">
                                    <select class="form-control" id="employee_dropdown" onchange="emp_onchange()">
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-2 form-label"><label>Reporting manager</label></div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <input class="form-control" type="text" id="reporting_manager" disabled/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-2 form-label"><label>HOD <span style="color: red;">*</span></label></div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <select class="form-control" id="hod_dropdown">
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-2 form-label"><label>Role Level</label></div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <input class="form-control" type="text" id="role_level"/>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="container-fluid" id="sp_add_header_container">
                        <div class="row header-input">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Header</label>
                                    <select class="form-control" id="header_list">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Name</label>
                                    <select class="form-control" id="header_emp_list"
                                            onchange="header_emp_list_onchange()">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Current Role</label>
                                    <input class="form-control" type="text" id="header_current_role" disabled/>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Role Level</label>
                                    <input class="form-control" type="text" id="header_role_level"/>
                                </div>
                            </div>
                        </div>
                        <div class="row header-input">
                            <div class="col-md-10">
                            </div>
                            <div class="col-md-2">
                                <input type="button" class="btn btn-primary" value="Add" style="width: 100%;"
                                       id="sp_header_add_btn" onclick="sp_header_add()"/>
                            </div>
                        </div>
                        <div class="row">

                            <div class="table-responsive">
                                <table id="succession_plan_header_table" class="<?php echo table_class() ?>">
                                    <thead>
                                    <tr>
                                        <th style="">#</th>
                                        <th style="">Header</th>
                                        <th style="">Name</th>
                                        <th style="">Current Role</th>
                                        <th style="">Role Level</th>
                                        <th style=""></th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    Close
                </button>
                <button id="save_plan_as_draft_btn" type="button" class="btn btn-default"
                        onclick="save_plan_as_draft()">Save
                </button>
                <button id="confirm_plan_btn" type="button" class="btn btn-default btn-success"
                        onclick="confirm_plan()">Confirm
                </button>
            </div>

        </div>
    </div>
</div>

<script type="text/javascript">

    var app = {};
    app.header_id = '';
    app.segment_id = '';
    app.form_status = '';

    $('.headerclose').click(function () {
        fetchPage('system/hrm/succession_planning', '0', 'HRMS');
    });

    function back_btn() {
        fetchPage('system/hrm/succession_planning', '0', 'HRMS');
    }

    $(document).ready(function () {

        $('#segment_year').datepicker({
            minViewMode: 2,
            format: 'yyyy'
        });

        $('#segment_year').keypress(function (e) {
            e.preventDefault();
        });

        succession_plan_table();
        app.segment_id = localStorage.getItem('segment_id');
        let title = ' - ' + localStorage.getItem('segment_description');
        $(".box-header").prepend('<i class="fa fa-arrow-left back" onclick="back_btn()"></i> ');
        $("#box-header-title").append(title);
        //load_years();
        get_segment_by_id(app.segment_id);
        get_designations();
        get_hod();
        $("#designation_dropdown").select2({
            placeholder: 'Select an option',
            tags: true,
            dropdownParent: $("#add_plan_dialog")
        });

        $("#employee_dropdown").select2({
            placeholder: 'Select an option',
            tags: true,
            dropdownParent: $("#add_plan_dialog")
        });

        $("#hod_dropdown").select2({
            placeholder: 'Select an option',
            tags: true,
            dropdownParent: $("#add_plan_dialog")
        });


    });

    function save_plan_as_draft() {
        save_plan(app.form_status, 0);
    }

    function confirm_plan() {
        if (is_approval_setup_exist()) {
            if (is_headers_exist()) {
                save_plan(app.form_status, 1);
            } else {
                myAlert('e', 'Cannot confirm a succession plan without headers.');
            }
        } else {
            myAlert('e', 'Approval setup not configured.');
        }
    }

    function is_headers_exist() {
        app.is_headers_exist = true;
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: {'spAutoID': app.spAutoID},
            url: '<?php echo site_url('Employee/is_sp_headers_exist') ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (data.status == false) {
                    app.is_headers_exist = false;
                }
                stopLoad();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
        return app.is_headers_exist;
    }

    function is_approval_setup_exist() {
        app.is_approval_setup_exist = true;
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: {},
            url: '<?php echo site_url('Employee/is_sp_approval_setup_exist') ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (data.status == false) {
                    app.is_approval_setup_exist = false;
                }
                stopLoad();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
        return app.is_approval_setup_exist;
    }

    function validate_succession_plan() {
        let is_valid = true;
        let designation_dropdown = $("#designation_dropdown").val();
        let employee_dropdown = $("#employee_dropdown").val();
        let hod_dropdown = $("#hod_dropdown").val();
        let year = $("#segment_year").val();

        if (year == "") {
            is_valid = false;
        }

        if (designation_dropdown == "") {
            is_valid = false;
        }

        if (employee_dropdown == "") {
            is_valid = false;
        }

        if (hod_dropdown == "") {
            is_valid = false;
        }
        return is_valid;
    }

    function save_plan(form_status, is_confirmed) {
        if (validate_succession_plan() == true) {
            let segmentID = app.segment_id;
            let empID = $("#employee_dropdown").val();
            let currentDesignationID = $("#designation_dropdown").val();
            let reportingManagerID = app.manager_id;
            let hodID = $("#hod_dropdown").val();
            let roleLevel = $("#role_level").val();
            let year = $("#segment_year").val();
            $.ajax({
                async: false,
                type: 'post',
                dataType: 'json',
                data: {
                    'segmentID': segmentID,
                    'empID': empID,
                    'currentDesignationID': currentDesignationID,
                    'reportingManagerID': reportingManagerID,
                    'hodID': hodID,
                    'roleLevel': roleLevel,
                    'form_status': form_status,
                    'spAutoID': app.spAutoID,
                    'is_confirmed': is_confirmed,
                    'year':year
                },
                url: '<?php echo site_url('Employee/save_succession_plan') ?>',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    succession_plan_table();
                    $("#add_plan_dialog").modal('hide');
                    myAlert('s', data.message);
                    stopLoad();
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                }
            });
        } else {
            myAlert('e', 'Please fill required fields.');
        }
    }

    function emp_onchange() {
        let emp_id = document.getElementById("employee_dropdown").value;
        if (emp_id != "") {
            $.ajax({
                async: false,
                type: 'post',
                dataType: 'json',
                data: {'emp_id': emp_id},
                url: '<?php echo site_url('Employee/get_emp_manager') ?>',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    let id = data.EIdNo;
                    let name = data.Ename1;
                    $("#reporting_manager").val(name);
                    app.manager_id = id;
                    stopLoad();
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                }
            });
        }
    }

    function get_designations() {
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: {'segment_id': app.segment_id},
            url: '<?php echo site_url('Employee/get_designations') ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                let options = "";
                options += '<option value="">Select an designation</option>';
                data.map(function (item, index) {
                    options += '<option value="' + item.DesignationID + '">' + item.DesDescription + '</option>';
                });
                $("#designation_dropdown").html(options);
                stopLoad();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function designation_onchange() {
        get_employees();
    }

    function header_emp_list() {
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: {'segment_id': app.segment_id},
            url: '<?php echo site_url('Employee/get_employees_filt_seg') ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                let options = "";
                options += '<option value=""></option>';
                data.map(function (item, index) {
                    options += '<option value="' + item.EIdNo + '" onchange="">' + item.Ename1 + '</option>';
                });
                $("#header_emp_list").html(options);
                stopLoad();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function get_hod() {
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: {'segment_id': app.segment_id},
            url: '<?php echo site_url('Employee/get_employees_filt_seg') ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                let options = "";
                options += '<option value="">Select the HOD</option>';
                data.map(function (item, index) {
                    options += '<option value="' + item.EIdNo + '" onchange="">' + item.Ename1 + ' - ' + item.ECode + '</option>';
                });
                $("#hod_dropdown").html(options);
                stopLoad();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function get_employees() {
        let designation_id = $("#designation_dropdown").val();
        if (designation_id != "") {
            $.ajax({
                async: false,
                type: 'post',
                dataType: 'html',
                data: {'segment_id': app.segment_id, 'designation_id': designation_id},
                url: '<?php echo site_url('Employee/get_employees_filt_seg_desig') ?>',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    // let options = "";
                    // options += '<option value="">Select an employee</option>';
                    // data.map(function (item, index) {
                    //     options += '<option value="' + item.EIdNo + '" onchange="">' + item.Ename1 + '</option>';
                    // });
                    $("#employee_dropdown_div").html(data);
                    $("#employee_dropdown").select2({
                        placeholder: 'Select an option',
                        tags: true,
                        dropdownParent: $("#add_plan_dialog")
                    });
                    stopLoad();
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                }
            });
        }

    }

    function get_segment_by_id(segment_id) {
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: {'segment_id': app.segment_id},
            url: '<?php echo site_url('Employee/get_segment_by_id') ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                let seg_description = data.description;
                $("#segment").val(seg_description);
                stopLoad();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function load_add_plan_dialog() {
        app.form_status = 'new';
        //clear
        $("#reporting_manager").val('');
        $("#role_level").val('');

        //dd
        $('#designation_dropdown').val('').trigger('change');
        $('#employee_dropdown').val('').trigger('change');
        $('#hod_dropdown').val('').trigger('change');

        $("#save_plan_as_draft_btn").show();
        $("#confirm_plan_btn").show();
        $(".header-input").show();
        $("#designation_dropdown").prop("disabled", false);
        $("#employee_dropdown").prop("disabled", false);
        //$("#reporting_manager").prop("disabled", false);
        $("#hod_dropdown").prop("disabled", false);
        $("#role_level").prop("disabled", false);
        $("#sp_add_header_container").hide();
        $("#confirm_plan_btn").hide();
        $("#add_plan_dialog").modal('show');
    }


    function succession_plan_header_table(spAutoID) {
        var Otable = $('#succession_plan_header_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bFilter": false,
            "bLengthChange": false,
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/succession_plan_header_table'); ?>",
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
            "aoColumns": [
                {"mData": "spAutoID"},
                {"mData": "header_description"},
                {"mData": "Ename1"},
                {"mData": "DesDescription"},
                {"mData": "roleLevel"},
                {"mData": "action"}
            ],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "spAutoID", "value": spAutoID});
                //aoData.push({ "name": "subcategory","value": $("#subcategory").val()});
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

    function succession_plan_table() {
        var Otable = $('#succession_plan_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/load_succession_plans'); ?>",
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
            "aoColumns": [
                {"mData": "spAutoID"},
                {"mData": "documentsystemCode"},
                {"mData": "segment_des"},
                {"mData": "year"},
                {"mData": "Ename1"},
                {"mData": "DesDescription"},
                {"mData": "createdDateTime"},
                {"mData": "createdUserName"},
                {"mData": "confirmed_status"},
                {"mData": "view_btn"}
            ],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "segmentID", "value": app.segment_id});
                //aoData.push({ "name": "subcategory","value": $("#subcategory").val()});
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


    function show_error(errorDivId, errorMessage) {
        var divSelector = "#" + errorDivId;
        $(divSelector).html(errorMessage);
    }

    function hide_error(errorDivId) {
        var divSelector = "#" + errorDivId;
        $(divSelector).html("");
    }

    function delete_sp(spAutoID) {
        bootbox.confirm({
            message: "Are you sure you want to delete this succession plan?",
            buttons: {
                confirm: {
                    label: 'Yes',
                    className: 'btn-success'
                },
                cancel: {
                    label: 'No',
                    className: 'btn-danger'
                }
            },
            callback: function (user_confirmation) {
                if (user_confirmation) {
                    startLoad();
                    $.ajax({
                        async: false,
                        type: 'post',
                        dataType: 'json',
                        data: {'spAutoID': spAutoID},
                        url: '<?php echo site_url('Employee/delete_sp') ?>',
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            if (data.status == 'success') {
                                myAlert('s', data.message);
                            } else {
                                myAlert('e', data.message);
                            }
                            succession_plan_table();
                            stopLoad();
                        }, error: function () {
                            alert('An Error Occurred! Please Try Again.');
                            /*An Error Occurred! Please Try Again*/
                            stopLoad();
                        }
                    });
                }
            }
        });


    }

    function delete_sph(documentHeaderID) {
        bootbox.confirm({
            message: "Are you sure you want to delete this succession plan header?",
            buttons: {
                confirm: {
                    label: 'Yes',
                    className: 'btn-success'
                },
                cancel: {
                    label: 'No',
                    className: 'btn-danger'
                }
            },
            callback: function (user_confirmation) {
                if (user_confirmation) {
                    startLoad();
                    $.ajax({
                        async: false,
                        type: 'post',
                        dataType: 'json',
                        data: {'documentHeaderID': documentHeaderID},
                        url: '<?php echo site_url('Employee/delete_sph') ?>',
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            if (data.status == 'success') {
                                myAlert('s', data.message);
                            } else {
                                myAlert('e', data.message);
                            }
                            succession_plan_header_table(app.spAutoID);
                            stopLoad();
                        }, error: function () {
                            alert('An Error Occurred! Please Try Again.');
                            /*An Error Occurred! Please Try Again*/
                            stopLoad();
                        }
                    });
                }
            }
        });


    }

    function view_sp(spAutoID) {
        app.form_status = 'modify';
        $("#sp_add_header_container").show();
        $("#add_plan_dialog").modal('show');
        header_emp_list();
        header_list();
        $("#header_list").select2({
            placeholder: 'Select an option',
            tags: true,
            dropdownParent: $("#add_plan_dialog")
        });
        $("#header_emp_list").select2({
            placeholder: 'Select an option',
            tags: true,
            dropdownParent: $("#add_plan_dialog")
        });
        succession_plan_header_table(spAutoID);
        app.spAutoID = spAutoID;
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: {'spAutoID': spAutoID},
            url: '<?php echo site_url('Employee/get_sp_by_id') ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $("#role_level").val(data.roleLevel);
                $("#reporting_manager").val(data.reportingManagerName);
                $('#hod_dropdown').val(data.hodID).trigger('change');
                $("#designation_dropdown").val(data.currentDesignationID);
                // $("#designation_dropdown").select2().select2('val',data.currentDesignationID);
                $("#designation_dropdown").trigger('change');
                $("#employee_dropdown").val(data.empID).trigger('change');
                $("#segment_year").val(data.year);
                app.manager_id = data.reportingManagerID;
                if (data.confirmedYN == '1') {
                    $("#save_plan_as_draft_btn").hide();
                    $("#confirm_plan_btn").hide();
                    $(".header-input").hide();
                    $("#designation_dropdown").prop("disabled", true);
                    $("#employee_dropdown").prop("disabled", true);
                    //$("#reporting_manager").prop("disabled", true);
                    $("#hod_dropdown").prop("disabled", true);
                    $("#role_level").prop("disabled", true);
                } else {
                    $("#save_plan_as_draft_btn").show();
                    $("#confirm_plan_btn").show();
                    $(".header-input").show();
                    $("#designation_dropdown").prop("disabled", false);
                    $("#employee_dropdown").prop("disabled", false);
                    //$("#reporting_manager").prop("disabled", false);
                    $("#hod_dropdown").prop("disabled", false);
                    $("#role_level").prop("disabled", false);
                }
                stopLoad();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function sp_header_add() {
        let header_id = $("#header_list").val();
        let header_emp_id = $("#header_emp_list").val();
        let header_current_role = app.header_current_role;
        let header_role_level = $("#header_role_level").val();
        if (validate_emp_header()) {
            $.ajax({
                async: false,
                type: 'post',
                dataType: 'json',
                data: {
                    'header_id': header_id,
                    'header_emp_id': header_emp_id,
                    'header_current_role': header_current_role,
                    'header_role_level': header_role_level,
                    'spAutoID': app.spAutoID
                },
                url: '<?php echo site_url('Employee/sp_header_add') ?>',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {

                    $("#header_list").val("").trigger('change');
                    $("#header_emp_list").val("").trigger('change');
                    $("#header_role_level").val("");
                    $("#header_current_role").val("");

                    succession_plan_header_table(app.spAutoID);
                    myAlert('s', data.message);
                    stopLoad();
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                }
            });
        }
    }

    function validate_emp_header() {
        let header_id = $("#header_list").val();
        let header_emp_id = $("#header_emp_list").val();
        let header_role_level = $("#header_role_level").val();
        app.is_valid = true;

        if(header_id==""){
            myAlert('e', 'Header is required.');
            app.is_valid = false;
            return app.is_valid;
        }

        if(header_emp_id==""){
            myAlert('e', 'Employee is required.');
            app.is_valid = false;
            return app.is_valid;
        }

        if(header_role_level==""){
            myAlert('e', 'Role Level is required.');
            app.is_valid = false;
            return app.is_valid;
        }

        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: {
                'header_id': header_id,
                'header_emp_id': header_emp_id,
                'spAutoID': app.spAutoID
            },
            url: '<?php echo site_url('Employee/validate_emp_header') ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                app.is_valid = data.status;
                if (data.status == false) {
                    myAlert('e', 'Cannot duplicate a header with the same employee.');
                }
                stopLoad();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
        return app.is_valid;
    }

    function header_emp_list_onchange() {
        let emp_id = $("#header_emp_list").val();
        if (emp_id != "") {
            $.ajax({
                async: false,
                type: 'post',
                dataType: 'json',
                data: {'emp_id': emp_id},
                url: '<?php echo site_url('Employee/get_emp_designation') ?>',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#header_current_role').val(data.DesDescription);
                    app.header_current_role = data.DesignationID;
                    stopLoad();
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                }
            });
        }
    }

    function header_list() {
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: {},
            url: '<?php echo site_url('Employee/get_header_list') ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                let options = "";
                options += '<option value=""></option>';
                data.map(function (item, index) {
                    options += '<option value="' + item.headerID + '" onchange="">' + item.description + '</option>';
                });
                $("#header_list").html(options);

                stopLoad();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function refer_back_confirmed_plan(spAutoID) {
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: {'spAutoID': spAutoID},
            url: '<?php echo site_url('Employee/refer_back_confirmed_plan') ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (data.status == 'success') {
                    myAlert('s', data.message);
                } else {
                    myAlert('e', data.message);
                }
                succession_plan_table();
                stopLoad();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    // function load_years() {
    //     //segment_year
    //     var d = new Date();
    //     var this_year = d.getFullYear();
    //     let options = '<option value="' + this_year + '">' + this_year + '</option>';
    //     let i;
    //     for (i = 2; i < 13; i++) {
    //         let year = this_year + i;
    //         options += '<option value="' + year + '">' + year + '</option>';
    //     }
    //     $("#segment_year").html(options);
    // }


</script>

