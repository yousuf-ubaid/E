<?php
/** Translation added */
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('appraisal_lang', $primaryLanguage);
$this->lang->load('profile', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('appraisal_master_employee_wise_performance');
echo head_page($title, false);

?>
<style>
    .error-message {
        color: red;
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
                <td>
                    <span class="label label-danger">&nbsp;</span> <?php echo $this->lang->line('common_not_approved'); ?>
                    <!--Not Approved-->
                </td>
            </tr>
        </table>
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-center">
        <?php echo form_dropdown('approvedYN', array('0' => $this->lang->line('common_pending'), '1' => $this->lang->line('common_approved')), '', 'class="form-control" id="approvedYN" required onchange="expense_claim_table()"'); ?>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="expanse_claim_approval_table" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 15%"><?php echo $this->lang->line('appraisal_master_deparment_appraisal_docref_column'); ?><!--Doc Ref--></th>
            <th style="min-width: 15%"><?php echo $this->lang->line('appraisal_master_corporate_goal_title'); ?><!--Corporate Goal--></th>
            <th style="min-width: 40%"><?php echo $this->lang->line('common_department'); ?><!--department--></th>
            <th style="min-width: 15%"><?php echo $this->lang->line('common_employee'); ?><!--Employee--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_status'); ?><!--Status--></th>
            <th style="min-width: 10%"><?php echo $this->lang->line('common_action'); ?><!--Action--></th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div class="modal fade" id="expense_claim_Approval_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo $this->lang->line('profile_expense_claim_approval'); ?><!--Expense Claim Approval--></h4>
            </div>
            <form class="form-horizontal" id="ec_approval_form">
                <div class="modal-body">
                    <div class="col-sm-1">
                        <!-- Nav tabs -->
                        <ul class="zx-nav zx-nav-tabs zx-tabs-left zx-vertical-text">
                            <li id="po_attachement_approval_Tabview_v" class="active"><a href="#Tab-home-v"
                                                                                         data-toggle="tab"
                                                                                         onclick="tabView()">
                                    <?php echo $this->lang->line('common_view'); ?><!--View--></a></li>
                            <li id="po_attachement_approval_Tabview_a"><a href="#Tab-profile-v" data-toggle="tab"
                                                                          onclick="tabAttachement()">
                                    <?php echo $this->lang->line('common_attachments'); ?><!--Attachment--></a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-sm-11">
                        <div class="zx-tab-content">
                            <div class="zx-tab-pane active" id="Tab-home-v">
                                <div id="conform_body"></div>
                                <hr>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-2 control-label">
                                        <?php echo $this->lang->line('common_status'); ?><!--Status--></label>

                                    <div class="col-sm-4">
                                        <?php echo form_dropdown('ec_status', array('' => $this->lang->line('common_please_select')/*'Please Select'*/, '1' => $this->lang->line('common_approved')/*'Approved'*/, '2' => $this->lang->line('common_refer_back')/*'Referred-back'*/), '', 'class="form-control" id="ec_status" required'); ?>
                                        <input type="hidden" name="Level" id="Level">
                                        <input type="hidden" name="expenseClaimMasterAutoID"
                                               id="expenseClaimMasterAutoID">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputPassword3" class="col-sm-2 control-label">
                                        <?php echo $this->lang->line('common_comment'); ?><!--Comments--></label>

                                    <div class="col-sm-8">
                                        <textarea class="form-control" rows="3" name="comments"
                                                  id="comments"></textarea>
                                    </div>
                                </div>
                                <div class="pull-right">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">
                                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                                    <button type="submit" class="btn btn-primary">
                                        <?php echo $this->lang->line('common_submit'); ?><!--Submit--></button>
                                </div>
                            </div>
                            <div class="tab-pane hide" id="Tab-profile-v">
                                <div class="table-responsive">
                                    <span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span>
                                    &nbsp; <strong>
                                        <?php echo $this->lang->line('common_expense_claim_attachments'); ?><!--Expense Claim Attachments--></strong>
                                    <br><br>
                                    <table class="table table-striped table-condensed table-hover">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>
                                                <?php echo $this->lang->line('common_file_name'); ?><!--File Name--></th>
                                            <th>
                                                <?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                                            <th><?php echo $this->lang->line('common_type'); ?><!--Type--></th>
                                            <th><?php echo $this->lang->line('common_action'); ?><!--Action--></th>
                                        </tr>
                                        </thead>
                                        <tbody id="ec_attachment_body" class="no-padding">
                                        <tr class="danger">
                                            <td colspan="5" class="text-center">
                                                <?php echo $this->lang->line('common_no_records_found'); ?><!--No Attachment Found--></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">&nbsp;
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="approval_dialog" role="dialog"
     aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="CommonEdit_Title">Employee Wise Performance Approval</h4>
            </div>

            <div class="modal-body" style="overflow-y: scroll;height: 500px;">
                <table id="employee_wise_performance_table" class="<?php echo table_class(); ?>">
                    <thead>
                    <tr>
                        <th style="min-width: 15%;border-radius: 3px 0 0 0;">
                            <?php echo $this->lang->line("appraisal_activity_department_appraisal_corporate_goal_department_objective_task_description"); ?>
                        </th>
                        <th style="min-width: 15%;border-radius: 0 0 0 0;">
                            <?php echo $this->lang->line("appraisal_activity_department_appraisal_corporate_goal_department_objective_weight"); ?>
                        </th>
                        <th style="min-width: 15%;border-radius: 0 0 0 0;">
                            <?php echo $this->lang->line("appraisal_activity_department_appraisal_corporate_goal_department_objective_assigned_employee"); ?>
                        </th>
                        <th style="min-width: 15%;border-radius: 0 0 0 0;">
                            <?php echo $this->lang->line("appraisal_activity_department_appraisal_corporate_goal_department_objective_date_to_complete"); ?>
                        </th>
                        <th style="min-width: 15%;border-radius: 0 0 0 0;">
                            <?php echo $this->lang->line("appraisal_activity_department_appraisal_corporate_goal_department_objective_is_approved_by_manager"); ?>
                        </th>
                        <th style="min-width: 15%;border-radius: 0 0 0 0;">
                            <?php echo $this->lang->line("appraisal_activity_department_appraisal_corporate_goal_department_objective_completion"); ?>
                        </th>
                        <th style="min-width: 15%;border-radius: 0 0 0 0;">
                            <?php echo $this->lang->line("appraisal_activity_department_appraisal_corporate_goal_department_objective_manager_review"); ?>
                        </th>
                    </tr>
                    </thead>
                </table>
                <div class="row">
                    <div class="form-group col-md-12">
                        <label><?php echo $this->lang->line('appraisal_master_deparment_appraisal_status_column'); ?></label>
                        <select name="status" class="form-control" id="approval_status" required="">
                            <option value=""
                                    selected="selected"><?php echo $this->lang->line('appraisal_please_select'); ?></option>
                            <option value="1"><?php echo $this->lang->line('appraisal_master_corporate_goal_approved_column'); ?></option>
                        </select>
                        <div id="status_error" class="error-message"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-12">
                        <label>Comment</label>

                        <textarea class="form-group col-md-12" id="approval_status_comment"></textarea>
                        <div id="comment_error" class="error-message"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-right m-t-xs">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?></button>
                    <button class="btn btn-primary" id="add_deparment_task"
                            onclick="save_approval_status.call(this);" type="button">
                        <?php echo $this->lang->line('common_save'); ?>
                    </button>

                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    app = {};
    app.employee_wise_performance_table = $('#employee_wise_performance_table').DataTable({
        "language": {
            "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
        },
    });
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/appraisal/approval/emp_wise_performance_approval', 'Test', '<?php echo $title; ?>')
        });
        expense_claim_table();
    });

    function refer_back_employee_wise_performance_confirm_dialog(masterID){
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
                        url: "<?php echo site_url('Appraisal/refer_back_employee_wise_performance'); ?>",
                        data: {masterID:masterID},
                        success: function (data) {
                            if(data.status==true){
                                myAlert('s', data.message);
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

    function expense_claim_table() {
        var Otable = $('#expanse_claim_approval_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Appraisal/get_employee_wise_performance_approval'); ?>",
            "aaSorting": [[1, 'desc']],
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
                {"mData": "perfomance_appraisal_id"},
                {"mData": "document_id"},
                {"mData": "narration"},
                {"mData": "DepartmentDes"},
                {"mData": "Ename1"},
                {"mData": "approved"},
                {"mData": "edit"}
            ],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "columnDefs": [{"targets": [0], "searchable": false}],
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

    function format_for_two_digits(num) {
        if (num < 10) {
            return '0' + num;
        } else {
            return num;
        }
    }

    function save_approval_status() {
        var master_id = app.master_id;
        var level = app.level;
        var code = 'APR-EWP';
        var status = $("#approval_status").val();
        var comment = $("#approval_status_comment").val();
        if (approval_modal_validation()) {
            $.ajax({
                dataType: "json",
                type: "POST",
                url: "<?php echo site_url('Appraisal/save_emp_performance_approval'); ?>",
                data: {
                    master_id: master_id,
                    level: level,
                    code: code,
                    status: status,
                    comment: comment
                },
                success: function (data) {
                    if(data.status==true){
                        myAlert('s', data.message);
                    }else{
                        myAlert('e', data.message);
                    }
                    $("#approval_dialog").modal('hide');
                    //quotation_contract_table();
                }
            });
        }
    }

    function approval_modal_validation() {
        hide_errors();
        var is_valid = true;
        var approval_status = $("#approval_status").val();
        var approval_status_comment = $("#approval_status_comment").val();

        if (approval_status == "") {
            show_error('status_error', '<?php echo $this->lang->line('common_status_is_required'); ?>');/*Status is Required*/
            is_valid = false;
        }
        if (approval_status_comment == "") {
            show_error('comment_error', '<?php echo $this->lang->line('common_comment_is_required'); ?>');/*Comment is Required*/
            is_valid = false;
        }
        return is_valid;
    }

    function fetch_approval(MasterID, level) {
        if (MasterID) {
            app.master_id = MasterID;
            app.level = level;

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {MasterID: MasterID},
                url: "<?php echo site_url('Appraisal/employee_wise_performance_approval_dialog'); ?>",
                beforeSend: function () {
                    //startLoad();
                },
                success: function (data) {

                    app.employee_wise_performance_table.clear().draw();
                    data.forEach(function (item, index) {
                        //preparing columns for datatable
                        var short_description = item.description.substring(0, 18) + '...';
                        var description_title = item.task_description + ' (' + item.description + ')';
                        var task_description = '<span title="' + description_title + '">' + item.task_description + ' (' + short_description + '</span>';
                        var weight = '<div style="text-align: center">' + item.weight + '%</div>';
                        var emp_name = item.Ename1;

                        var d = new Date(item.date_to_complete);
                        var month = format_for_two_digits((d.getMonth() + 1));
                        var date = format_for_two_digits(d.getDate());
                        var date_to_complete = d.getFullYear() + '-' + month + '-' + date;

                        var is_approved_by_manager = item.is_approved_by_manager;
                        var manager_approved_checkbox_status = "";
                        if (is_approved_by_manager == 1) {
                            manager_approved_checkbox_status = "checked";
                        }
                        var manager_approve_checkbox = '<input disabled data-task_id="' + item.id + '" type="checkbox" ' + manager_approved_checkbox_status + '/>';

                        var progress_bar_text_color = 'black';
                        if (item.completion >= 60) {
                            progress_bar_text_color = 'white';
                        }
                        var completion = '<div style="text-align: center">' + item.completion + '%</div>';

                        var manager_review = item.manager_review;
                        if (item.manager_review == 'pending') {
                            manager_review = '<?php echo $this->lang->line('common_pending'); ?>';/*Pending*/
                        } else if (item.manager_review == 'rejected') {
                            manager_review = '<?php echo $this->lang->line('common_rejected'); ?>';/*Rejected*/
                        } else if (item.manager_review == 'approved') {
                            manager_review = '<?php echo $this->lang->line('common_approved'); ?>';/*Approved*/
                        }

                        //append row into datatable
                        app.employee_wise_performance_table.row.add([task_description, weight, emp_name, date_to_complete, manager_approve_checkbox, completion, manager_review]).draw(false);

                    });
                    $("#approval_dialog").modal('show');
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    refreshNotifications(true);
                }
            });
        }
    }


    function tabAttachement() {
        $("#Tab-profile-v").removeClass("hide");
    }

    function tabView() {
        $("#Tab-profile-v").addClass("hide");
    }

    function hide_errors() {
        hide_error('status_error');
        hide_error('comment_error');
    }

    function hide_error(errorDivId) {
        var divSelector = "#" + errorDivId;
        $(divSelector).html("");
    }

    function show_error(errorDivId, errorMessage) {
        var divSelector = "#" + errorDivId;
        $(divSelector).html(errorMessage);
    }
</script>
