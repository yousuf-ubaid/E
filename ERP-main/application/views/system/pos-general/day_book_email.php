<?php
$role = load_crew_role_drop();
$employee = load_employee_for_crew_drop();
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_config_restaurant', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$this->load->library('pos_policy');
$get_outletID = get_outletID();
$current_companyID = current_companyID();
$isOutletTaxEnabled = isOutletTaxEnabled($get_outletID, $current_companyID);

?>
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/pos/pos-config.css'); ?>">
<style>
    .error-message {
        color: red;
    }

    td {
        text-align: center;
    }

    .act-btn-margin {
        margin: 0 2px;
    }
</style>
<div class="box box-warning">
    <div class="box-body">
        <div class="nav-tabs-custom">
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1">
                    <div class="posContainer">

                        <div style="font-size:16px; font-weight: 800;">
                            Email Configuration for Day Book Email<!--Point of Sales Settings-->
                            for <?php echo current_companyCode() ?></div>
                        <div class="row">
                            <div class="form-group col-sm-12">
                                <fieldset style="margin-top:5px;">
                                    <legend style="margin-bottom: 5px;font-size: 15px;font-weight: 600;">
                                        New Email
                                    </legend>
                                    <div class="form-group col-sm-4">
                                        <label for="stakeholder_name">
                                            Name<span title="required field"
                                                      style="color:red; font-weight: 600; font-size: 12px;">*</span></label>
                                        <input id="stakeholder_name" type="text" class="form-control"
                                               autocomplete="off">
                                        <div id="stakeholder_name_error" class="error-message"></div>
                                    </div>
                                    <div class="form-group col-sm-4">
                                        <label for="email">
                                            Email<span title="required field"
                                                       style="color:red; font-weight: 600; font-size: 12px;">*</span></label>
                                        <input id="email" type="text" class="form-control" autocomplete="off">
                                        <div id="email_error" class="error-message"></div>
                                    </div>
                                    <div class="form-group col-sm-2">
                                        <button class="btn btn-primary" id="save_email" onclick="save_email()" style="margin-top: 25px;">Save
                                        </button>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                        <hr>
                        <table class="<?php echo table_class_pos(4) ?>" id="tbl_dayBookEmail"
                               style="font-size:12px width:100%">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>

                    </div>
                </div>
                <!-- /.tab-pane -->
                <div class="tab-pane" id="tab_2">
                    <div id="menu_edit_container2"></div>

                </div>

            </div>
            <!-- /.tab-content -->
        </div>
    </div>
</div>

<div class="modal fade" id="email_edit_dialog" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="CommonEdit_Title">
                    <?php echo $this->lang->line('appraisal_master_subdepartment_column'); ?><!--Sub Department--></h4>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-sm-12">
                        <div class="form-group col-sm-4">
                            <label for="stakeholder_name_edit">
                                Name<span title="required field"
                                          style="color:red; font-weight: 600; font-size: 12px;">*</span></label>
                            <input id="stakeholder_name_edit" type="text" class="form-control"
                                   autocomplete="off">
                            <div id="stakeholder_name_edit_error" class="error-message"></div>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="email_edit">
                                Email<span title="required field"
                                           style="color:red; font-weight: 600; font-size: 12px;">*</span></label>
                            <input id="email_edit" type="text" class="form-control" autocomplete="off">
                            <div id="email_edit_error" class="error-message"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-right m-t-xs">
                    <button class="btn btn-primary" id="edit_email" onclick="edit_email()">Save
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript"
        src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>

<script>

    var app = {};

    $(document).ready(function (e) {
        loadEmailList();
    });

    function loadEmailList() {
        var Otable = $('#tbl_dayBookEmail').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Pos_restaurant/get_daybook_emails'); ?>",
            "aaSorting": [[0, 'asc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }
            },
            "aoColumns": [
                {"mData": "dayBookEmailID"},
                {"mData": "name"},
                {"mData": "email"},
                {"mData": "action"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "posType", "value": "gpos"});
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

    function open_email_edit_dialog() {
        hide_error('stakeholder_name_edit_error');
        hide_error('email_edit_error');
        let id = $(this).data('id');
        app.current_id = id;
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: {id: id},
            url: "<?php echo site_url('Pos_restaurant/get_daybook_email_details'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $("#stakeholder_name_edit").val(data.name);
                $("#email_edit").val(data.email);
                $("#email_edit_dialog").modal('show');
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
            }
        });
    }

    function edit_email() {
        let name = $("#stakeholder_name_edit").val();
        let email = $("#email_edit").val();
        if (validate_email_edit_form()) {
            $.ajax({
                async: false,
                type: 'post',
                dataType: 'json',
                data: {name: name, email: email, posType: 'gpos', id: app.current_id},
                url: "<?php echo site_url('Pos_restaurant/edit_daybook_email'); ?>",
                beforeSend: function () {

                },
                success: function (data) {
                    if (data.status == 'failed') {
                        myAlert('e', data.message);
                    } else {
                        myAlert('s', data.message);
                        $("#email_edit_dialog").modal('hide');
                        loadEmailList();
                    }

                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                }
            });
        }
    }

    function validate_email_edit_form() {
        let is_valid = true;
        let name = $("#stakeholder_name_edit").val();
        let email = $("#email_edit").val();
        if (name == "") {
            is_valid = false;
            show_error('stakeholder_name_edit_error', 'Name is Required.');
        } else {
            hide_error('stakeholder_name_edit_error');
        }

        if (email == "") {
            is_valid = false;
            show_error('email_edit_error', 'Email is Required.');
        } else {
            if (!validate_email_edit(email)) {
                is_valid = false;
            } else {
                hide_error('email_edit_error');
            }
        }
        return is_valid;
    }

    function validate_email_edit(email) {
        app.is_valid = true;
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: {email: email, posType: 'gpos'},
            url: "<?php echo site_url('Pos_restaurant/validate_daybook_email'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                if (data.status == 'failed') {
                    app.is_valid = false;
                    show_error('email_edit_error', data.message);
                } else {
                    hide_error('email_edit_error');
                }

            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
            }
        });
        return app.is_valid;
    }

    function validate_email(email) {
        app.is_valid = true;
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: {email: email, posType: 'gpos'},
            url: "<?php echo site_url('Pos_restaurant/validate_daybook_email'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                if (data.status == 'failed') {
                    app.is_valid = false;
                    show_error('email_error', data.message);
                } else {
                    hide_error('email_error');
                }

            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
            }
        });
        return app.is_valid;
    }

    function save_email() {
        let name = $("#stakeholder_name").val();
        let email = $("#email").val();
        if (validate_email_form()) {
            $.ajax({
                async: false,
                type: 'post',
                dataType: 'json',
                data: {name: name, email: email, posType: 'gpos'},
                url: "<?php echo site_url('Pos_restaurant/save_daybook_email'); ?>",
                beforeSend: function () {

                },
                success: function (data) {
                    if (data.status == 'failed') {
                        myAlert('e', data.message);
                    } else {
                        myAlert('s', data.message);
                        $("#stakeholder_name").val("");
                        $("#email").val("");
                        hide_error('stakeholder_name_error');
                        hide_error('email_error');
                        loadEmailList();
                    }

                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                }
            });
        }
    }

    function delete_email() {
        var email_id = $(this).data('id');
        bootbox.confirm({
            message: "Are you sure, you want to remove the email address?",/*Are you sure, you want to delete the template?*/
            buttons: {
                confirm: {
                    label: '<?php echo $this->lang->line('common_yes'); ?>',
                    className: 'btn-success'
                },
                cancel: {
                    label: '<?php echo $this->lang->line('common_no'); ?>',
                    className: 'btn-danger'
                }
            },
            callback: function (user_confirmation) {
                if (user_confirmation) {
                    startLoad();
                    $.ajax({
                        dataType: "json",
                        type: "POST",
                        url: "<?php echo site_url('Pos_restaurant/delete_email'); ?>",
                        data: {
                            email_id: email_id
                        },
                        success: function (data) {
                            if (data.status == 'success') {
                                loadEmailList();
                                myAlert('s', data.message);
                            } else if (data.status == 'failed') {
                                myAlert('w', data.message);
                            }
                            stopLoad();
                        },
                        error: function () {
                            myAlert('e', 'Error');
                            stopLoad();
                        }
                    });
                }
            }
        });
    }

    function validate_email_form() {
        let is_valid = true;
        let name = $("#stakeholder_name").val();
        let email = $("#email").val();
        if (name == "") {
            is_valid = false;
            show_error('stakeholder_name_error', 'Name is Required.');
        } else {
            hide_error('stakeholder_name_error');
        }

        if (email == "") {
            is_valid = false;
            show_error('email_error', 'Email is Required.');
        } else {
            if (!validate_email(email)) {
                is_valid = false;
            } else {
                hide_error('email_error');
            }
        }
        return is_valid;
    }


    function edit_dialog_popup() {

    }

    function show_error(errorDivId, errorMessage) {
        var divSelector = "#" + errorDivId;
        $(divSelector).html(errorMessage);
    }

    function hide_error(errorDivId) {
        var divSelector = "#" + errorDivId;
        $(divSelector).html("");
    }

</script>

