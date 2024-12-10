<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('appraisal_lang', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('appraisal_approval_corporate_goal_title');
echo head_page($title, false);

//echo head_page('Quotation / Contract Approval', false);


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
        <?php echo form_dropdown('approvedYN', array('0' => $this->lang->line('common_pending')/*'Pending'*/, '1' => $this->lang->line('common_approved')/*'Approved'*/), '', 'class="form-control" id="approvedYN" required onchange="quotation_contract_table()"'); ?>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="quotation_contract_approval_table" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 5%"><?php echo $this->lang->line('appraisal_master_deparment_appraisal_docref_column'); ?></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('appraisal_master_corporate_goal_narration_column'); ?></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('appraisal_activity_create_corporate_goal_created_date_field'); ?></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('appraisal_master_corporate_goal_from_date_column'); ?></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('appraisal_activity_create_corporate_goal_to_date_field'); ?></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('appraisal_master_corporate_goal_confirmed_column'); ?></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('appraisal_master_corporate_goal_approved_column'); ?></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('appraisal_master_subdepartment_actions_column'); ?></th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div class="modal fade" id="corporate_goal_approval_modal" role="dialog"
     aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-lg" style="width:53%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"
                    id="CommonEdit_Title"><?php echo $this->lang->line('appraisal_approval_corporate_goal_title'); ?></h4>
            </div>
            <div class="modal-body" style="height: 150px;">
                <div class="tab-content">
                    <div id="step1" class="tab-pane active">
                        <input type="hidden" id="supplierCreditPeriodhn" name="supplierCreditPeriodhn">
                        <div class="row">
                            <div class="form-group col-sm-8">
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="document_id"><?php echo $this->lang->line('appraisal_activity_department_appraisal_document_id'); ?>
                                    : </label>
                                <span id="document_id_read_only"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-8">
                                <label for="narration">
                                    <?php echo $this->lang->line('appraisal_activity_create_corporate_goal_narration_field'); ?><!--Invoice Type--> <?php required_mark(); ?></label>
                                <div id="narration_read_only"></div>
                            </div>
                            <div class="form-group col-sm-4" id="created_date_form_group">
                                <label for="created_date">
                                    <?php echo $this->lang->line('appraisal_activity_create_corporate_goal_created_date_field'); ?></label>
                                <div id="created_date_read_only"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-12">
                                <fieldset style="margin-top:5px;">
                                    <legend style="margin-bottom: 5px;font-size: 15px;font-weight: 600;">
                                        <?php echo $this->lang->line('appraisal_appraisal_period'); ?>
                                    </legend>
                                    <div class="form-group col-sm-4">
                                        <label for="from_date">
                                            <?php echo $this->lang->line('appraisal_activity_create_corporate_goal_from_date_field'); ?><?php required_mark(); ?></label>
                                        <div id="from_date_read_only"></div>
                                    </div>
                                    <div class="form-group col-sm-4">
                                        <label for="to_date">
                                            <?php echo $this->lang->line('appraisal_activity_create_corporate_goal_to_date_field'); ?><?php required_mark(); ?></label>
                                        <div id="to_date_read_only"></div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>

                    </div>
                </div>
                <div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <table id="goal_objectives_table_read_only" class="<?php echo table_class(); ?>">
                                <thead>
                                <tr>
                                    <th style="min-width: 15%">
                                        <?php echo $this->lang->line('appraisal_objective'); ?>
                                    </th>
                                    <th style="min-width: 15%">
                                        <?php echo $this->lang->line('appraisal_weight'); ?>
                                    </th>
                                    <th style="min-width: 15%">
                                        <?php echo $this->lang->line('appraisal_assigned_department'); ?>
                                    </th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>


                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div id="softskills_template_read_only">
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
                        <label><?php echo $this->lang->line('common_comment'); ?><!--Comment--></label>

                        <textarea class="form-group col-md-12" id="approval_status_comment"></textarea>
                        <div id="comment_error" class="error-message"></div>
                    </div>
                    <div class="form-group col-md-12">
                        <button class="btn btn-primary col-md-3 pull-right"
                                onclick="save_approval_status.call(this);" type="button">
                            <?php echo $this->lang->line('common_save'); ?>
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
    app.goal_objectives_table_read_only = $('#goal_objectives_table_read_only').DataTable({
        "language": {
            "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
        },
    });
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/appraisal/approval/corporate_goal_approval', '', '<?php echo $title ?>');
        });
        quotation_contract_table();
    });

    function load_softskills_template_read_only(template_id) {
        $.ajax({
            dataType: "json",
            type: "POST",
            url: "<?php echo site_url('Appraisal/get_softskills_template_details'); ?>",
            data: {template_id: template_id},
            success: function (data) {
                var template_body = '<table class="table table-striped table-bordered" id="template_table_read_only"><thead><tr>';
                template_body += '<th>Performance Area</th>';
                data.skills_grades_list.forEach(function (item, index) {
                    template_body += '<th>' + item.grade + '</th>';
                });
                template_body += '</tr></thead>' +
                    '<tbody id="table_body_read_only"></tbody></table>';
                $("#softskills_template_read_only").html(template_body);

                var table_body = "";
                data.skills_performance_area_list.forEach(function (item, index) {
                    table_body += '<tr>' +
                        '<td>' + item.performance_area + '</td>';
                    for (var i = 1; i <= data.skills_grades_list.length; i++) {
                        table_body += '<td></td>';
                    }
                    table_body += '</tr>'
                });
                $("#table_body_read_only").html(table_body);
            }
        });
    }

    function corporate_goal_view_popup(goal_id) {
        //app.form_status = 'edit';
        app.id_list_for_delete = [];//old values are cleared
        app.goal_id = goal_id;
        $('#created_date_form_group').show();//this field only showing in edit mode.
        $('#goal_objectives_table_read_only_wrapper').hide();
        corporate_goal_form_hide_errors();
        $.ajax({
            dataType: "json",
            type: "POST",
            url: "<?php echo site_url('Appraisal/get_corporate_goal_details'); ?>",
            data: {goal_id: app.goal_id},
            success: function (data) {

                $('#document_id_read_only').html(data.goal_details[0].document_id);

                $('#narration_read_only').html(data.goal_details[0].narration);

                var d = new Date(data.goal_details[0].from);
                from = (d.getMonth() + 1) + '/' + d.getDate() + '/' + d.getFullYear();
                $('#from_date_read_only').html(from);
                app.from_date = from;

                var d = new Date(data.goal_details[0].to);
                to = (d.getMonth() + 1) + '/' + d.getDate() + '/' + d.getFullYear();
                $('#to_date_read_only').html(to);
                app.to_date = to;

                var d = new Date(data.goal_details[0].created_date);
                created_date = (d.getMonth() + 1) + '/' + d.getDate() + '/' + d.getFullYear();
                $('#created_date_read_only').html(created_date);

                if (data.goal_objectives.length > 0) {
                    $('#goal_objectives_table_read_only_wrapper').show();
                }
                app.goal_objectives_table_read_only.clear().draw();

                data.goal_objectives.map(function (value, index) {
                    var corporate_objective_id = value['objective_master_id'];
                    var obejctive_mapping_id = value['objective_mapping_id'];
                    var departments_drop_down_list_html = generated_dropdown_list_options_for_department(value['DepartmentMasterID']);
                    corporate_objective_description = '<span class="goal_objective_description" id="' + obejctive_mapping_id + '" data-corporate_objective_id="' + corporate_objective_id + '">' + value['objective_description'] + '</span>';
                    var departments_dropdown = '<div style="text-align: center">' + value['DepartmentDes'] + '</div>';

                    var weight_input = '<div style="text-align: center">' + value['weight'] + ' %</div>';
                    app.goal_objectives_table_read_only.row.add([corporate_objective_description, weight_input, departments_dropdown]).draw(false);
                });

                var approved_status = $("#approvedYN").val();
                if (approved_status == 1) {
                    $("#approval_form_section").hide();
                } else {
                    $("#approval_form_section").show();
                }
                //$("#corporate_goal_modal_read_only_view").modal('show');
                if(data.goal_details[0].softskills_template_id!=0){
                    load_softskills_template_read_only(data.goal_details[0].softskills_template_id);
                }else{
                    $("#softskills_template_read_only").html("");
                }
            }
        });


    }

    function quotation_contract_table() {
        var Otable = $('#quotation_contract_approval_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Appraisal/fetch_corporate_goal_approval'); ?>",
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
                {"mData": "goal_id"},
                {"mData": "document_id"},
                {"mData": "narration"},
                {"mData": "created_at"},
                {"mData": "from_date"},
                {"mData": "to_date"},
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

    function fetch_approval(contractAutoID, documentApprovedID, Level, code) {
        if (contractAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'contractAutoID': contractAutoID, 'html': true},
                url: "<?php echo site_url('Quotation_contract/load_contract_conformation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#contractAutoID').val(contractAutoID);
                    $('#documentApprovedID').val(documentApprovedID);
                    $('#code').val(code);
                    $('#Level').val(Level);
                    $("#Quotation_contract_modal").modal({backdrop: "static"});
                    $('#conform_body').html(data);
                    $('#comments').val('');
                    creditNote_attachment_View_modal(code, contractAutoID);
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

    function corporate_goal_approval_modal(goal_id, $i, level, $j) {
        app.goal_id = goal_id;
        app.level = level;
        corporate_goal_view_popup(goal_id);
        $("#corporate_goal_approval_modal").modal('show');
    }

    function save_approval_status() {
        var goal_id = app.goal_id;
        var level = app.level;
        var code = 'CG';
        var status = $("#approval_status").val();
        var comment = $("#approval_status_comment").val();

        if (approval_modal_validation()) {
            startLoad();
            $.ajax({
                dataType: "json",
                type: "POST",
                url: "<?php echo site_url('Appraisal/save_corporate_goal_approval'); ?>",
                data: {
                    goal_id: goal_id,
                    level: level,
                    code: code,
                    status: status,
                    comment: comment
                },
                success: function (data) {
                    stopLoad();
                    $("#corporate_goal_approval_modal").modal('hide');
                    if (data.status == false) {
                        myAlert('e', data.message);
                    } else {
                        myAlert('s', data.message);
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
            //show_error('status_error', 'Status is Required');
            show_error('status_error', '<?php echo $this->lang->line('common_status_is_required');?>');/*Status is Required*/

            is_valid = false;
        }
        if (approval_status_comment == "") {
            show_error('comment_error', '<?php echo $this->lang->line('common_comment_is_required');?>');/*Comment is Required*/
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

    function corporate_goal_form_hide_errors() {
        hide_error('total_weight_error');
        hide_error('objectives_list_error');

        hide_error('narrationError');
        hide_error('from_date_error');
        hide_error('to_date_error');
    }

    function generated_dropdown_list_options_for_department(selected_value) {
        var departments_drop_down_list_html;
        var departments = "";
        var data = JSON.parse(localStorage.getItem("departments"));
        if(data) {
            data.forEach(function (item, index) {
                var select_status
                if (selected_value == item.department_master_id) {
                    select_status = "selected";
                } else {
                    select_status = "";
                }
                departments += '<option ' + select_status + ' value="' + item.department_master_id + '" data-department_id="' + item.department_master_id + '">' + item.department_description + '</option>';
            });
        }
        departments_drop_down_list_html = departments;
        return departments_drop_down_list_html;
    }

</script>
