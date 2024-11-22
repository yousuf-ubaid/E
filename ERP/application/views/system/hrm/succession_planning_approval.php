<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('appraisal_lang', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = "Succession Plan Approval";
echo head_page($title, false);

//echo head_page('Quotation / Contract Approval', false);


?>
<style>
    .error-message {
        color: red;
    }
    .form-label {
        text-align: right;
    }
</style>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class() ?>">
            <tr>
                <td><span class="label label-success">&nbsp;</span>
                    <?php echo $this->lang->line('common_approved'); ?><!--Approved-->
                </td>
                <td><span class="label label-danger">&nbsp;</span>
                    <?php echo $this->lang->line('common_not_approved'); ?> <!--Not Approved-->
                </td>
            </tr>
        </table>
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-center">
        <?php echo form_dropdown('approvedYN', array('0' => $this->lang->line('common_pending')/*'Pending'*/, '1' => $this->lang->line('common_approved')/*'Approved'*/), '', 'class="form-control" id="approvedYN" required onchange="succession_plan_approval_table()"'); ?>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="succession_plan_approval_table" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 5%">Document Code</th>
            <th style="min-width: 5%">Segment</th>
            <th style="min-width: 5%">Designation</th>
            <th style="min-width: 5%"><?php echo $this->lang->line('appraisal_master_corporate_goal_confirmed_column'); ?></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('appraisal_master_corporate_goal_approved_column'); ?></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('appraisal_master_subdepartment_actions_column'); ?></th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div class="modal fade" id="succession_plan_approval_modal" role="dialog"
     aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-lg" style="width:53%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"
                    id="CommonEdit_Title">Succession Plan Approval</h4>
            </div>
            <div class="modal-body" style="">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-3 form-label">
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
                        <div class="col-md-3 form-label">
                            <label>Designation</label>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                            <input class="form-control" type="text" id="designation" disabled/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-3 form-label">
                            <label>Employee</label>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                            <input class="form-control" type="text" id="employee" disabled/>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-3 form-label">

                            <label>Reporting manager</label>

                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                            <input class="form-control" type="text" id="reporting_manager" disabled/>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-3 form-label">

                            <label>HOD</label>

                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <input class="form-control" type="text" id="hod" disabled/>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-3 form-label">

                            <label>Role Level</label>

                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <input class="form-control" type="text" id="role_level" disabled/>
                            </div>
                        </div>

                    </div>
                    <hr>

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
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>

                </div>
                <div class="row" id="approval_form_section">
                    <div class="form-group col-md-12">
                        <label><?php echo $this->lang->line('appraisal_master_deparment_appraisal_status_column'); ?></label>
                        <select name="status" class="form-control" id="approval_status" required="">
                            <option value=""
                                    selected="selected"><?php echo $this->lang->line('appraisal_please_select'); ?></option>
                            <option value="1"><?php echo $this->lang->line('appraisal_master_corporate_goal_approved_column'); ?></option>
                            <option value="2"><?php echo $this->lang->line('appraisal_reject'); ?></option>
                        </select>
                        <div id="status_error" class="error-message"></div>
                    </div>
                    <div class="form-group col-md-12">
                        <label>Comment</label>

                        <textarea class="form-group col-md-12" id="approval_status_comment"></textarea>
                        <div id="comment_error" class="error-message"></div>
                    </div>
                    <div class="form-group col-md-12">
                        <button class="btn btn-primary col-md-3 pull-right" id="approval_submit_btn"
                                onclick="save_approval_status.call(this);" type="button">
                            <?php echo $this->lang->line('common_submit'); ?>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="corporate_goal_modal_read_only_view" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"
                    id="CommonEdit_Title"><?php echo $this->lang->line('appraisal_master_corporate_goal_title'); ?></h4>
            </div>

            <div class="modal-body" style="overflow-y: scroll;height: 500px;">


            </div>
            <div class="modal-footer">
                <div class="text-right m-t-xs">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?></button>

                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    app = {};
    app.goal_objectives_table_read_only = $('#goal_objectives_table_read_only').DataTable();
    $(document).ready(function () {
        succession_plan_approval_table();
    });



    function succession_plan_approval_table() {
        var Otable = $('#succession_plan_approval_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/succession_plan_approval_table'); ?>",
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
                {"mData": "seg_des"},
                {"mData": "DesDescription"},
                {"mData": "confirmedYN"},
                {"mData": "approved"},
                {"mData": "edit"}
            ],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "approvedYN", "value": $("#approvedYN :checked").val()});
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

    function succession_plan_approval_modal(spAutoID, $i, level, $j,status) {
        app.spAutoID=spAutoID;
        app.level=level;
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: {'spAutoID': spAutoID},
            url: '<?php echo site_url('Employee/get_sp_approval_by_id') ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $("#segment").val(data.segment);
                $("#designation").val(data.DesDescription);
                $("#employee").val(data.emp_name);
                $("#reporting_manager").val(data.reportingManagerName);
                $("#hod").val(data.hod_name);
                $("#role_level").val(data.roleLevel);

                if(status=='view'){
                    $("#approval_status").prop('disabled', true);
                    $("#approval_status_comment").prop('disabled', true);
                    $("#approval_submit_btn").prop('disabled', true);

                }else{
                    $("#approval_status").prop('disabled', false);
                    $("#approval_status_comment").prop('disabled', false);
                    $("#approval_submit_btn").prop('disabled', false);
                }

                stopLoad();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
        succession_plan_header_table(spAutoID);
        $("#succession_plan_approval_modal").modal('show');
    }

    function save_approval_status() {

        let spAutoID = app.spAutoID;
        let level = app.level
        var code = 'SCP';
        var status = $("#approval_status").val();
        var comment = $("#approval_status_comment").val();

        if (approval_modal_validation()) {
            $.ajax({
                dataType: "json",
                type: "POST",
                url: "<?php echo site_url('Employee/save_succession_plan_approval'); ?>",
                data: {
                    goal_id: spAutoID,
                    level: level,
                    code: code,
                    status: status,
                    comment: comment
                },
                success: function (data) {
                    $("#succession_plan_approval_modal").modal('hide');
                    if (data.status == false) {
                        myAlert('e', data.message);
                    } else {
                        myAlert('s', data.message);
                        succession_plan_approval_table();
                    }
                    quotation_contract_table();
                }
            });
        }
    }

    function hide_errors() {
        hide_error('status_error');
        hide_error('comment_error');
    }

    function approval_modal_validation() {
        hide_errors();
        var is_valid = true;
        var approval_status = $("#approval_status").val();
        var approval_status_comment = $("#approval_status_comment").val();

        if (approval_status == "") {
            show_error('status_error', 'Status is Required');
            is_valid = false;
        }
        if (approval_status_comment == "") {
            show_error('comment_error', 'Comment is Required');
            is_valid = false;
        }
        return is_valid;
    }

    function show_error(errorDivId, errorMessage) {
        var divSelector = "#" + errorDivId;
        $(divSelector).html(errorMessage);
    }

    function hide_error(errorDivId) {
        var divSelector = "#" + errorDivId;
        $(divSelector).html("");
    }

    function succession_plan_header_table(spAutoID) {
        var Otable = $('#succession_plan_header_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bFilter":false,
            "bLengthChange":false,
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
                {"mData": "roleLevel"}
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

    function refer_back_succession_plan(masterID){
        bootbox.confirm({
            message: "Are you sure you want to refer back this item?",
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
                    $.ajax({
                        dataType: "json",
                        type: "POST",
                        url: "<?php echo site_url('Employee/refer_back_succession_plan'); ?>",
                        data: {masterID:masterID},
                        success: function (data) {
                            if(data.status==true){
                                myAlert('s', data.message);
                                succession_plan_approval_table();
                            }else{
                                myAlert('e', data.message);
                            }
                            //myAlert('s', 'Successfully Referred Back');
                        }
                    });

                }
            }
        });
    }

</script>
