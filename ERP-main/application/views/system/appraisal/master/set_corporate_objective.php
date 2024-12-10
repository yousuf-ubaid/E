<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('appraisal_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('appraisal_master_corporate_objective_title');


?>
<style>
    .error-message {
        color: red;
    }
</style>
<div class="row">
    <div class="col-md-12" id="sub-container">
        <div class="box">
            <div class="box-header with-border" id="box-header-with-border">
                <h3 class="box-title" id="box-header-title"><?php echo $title; ?></h3>
                <div class="box-tools pull-right">

                </div>
            </div>
            <link rel="stylesheet"
                  href="<?php echo base_url('plugins/Horizontal-Hierarchical/src/jquery.hortree.css'); ?>"/>

            <div class="row">
                <div class="col-md-5">

                </div>
                <div class="col-md-4 text-center">
                    &nbsp;
                </div>
                <div class="col-md-3 text-right">
                    <button style="margin-top: 5px;" type="button" class="btn btn-primary pull-right"
                            onclick="create_corporate_objective_btn_click.call(this)">
                        <!--Add New Supplier Invoices-->
                        <i class="fa fa-plus"></i>
                        <?php echo $this->lang->line('appraisal_master_create_corporate_objective_btn'); ?><!--Create Supplier Invoice-->
                    </button>
                </div>
            </div>
            <hr style="margin-top:5px;">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table id="corporate_objective_table" class="<?php echo table_class(); ?>">
                            <thead>
                            <tr>
                                <th style="min-width: 15%">
                                    <?php echo $this->lang->line('appraisal_corporate_objective_column'); ?><!--BSI Code--></th>
                                <th style="min-width: 15%">
                                    <?php echo $this->lang->line('appraisal_master_subdepartment_actions_column'); ?><!--Total Value--></th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>


            <div class="modal fade" id="corporate_objective_modal" role="dialog" aria-labelledby="mySmallModalLabel">
                <div class="modal-dialog modal-lg" style="width:33%">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                        aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title" id="CommonEdit_Title"><?php echo $this->lang->line('appraisal_master_corporate_objective_title'); ?></h4>
                        </div>

                        <div class="modal-body">
                            <div class="tab-content">
                                <div id="step1" class="tab-pane active">
                                    <input type="hidden" id="supplierCreditPeriodhn" name="supplierCreditPeriodhn">
                                    <div class="row">
                                        <div class="form-group col-sm-12">
                                            <label for="invoiceType">
                                                <?php echo $this->lang->line('appraisal_master_create_corporate_objective_description_field'); ?><!--Invoice Type--> <?php required_mark(); ?></label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-sm-10">
                                            <input type="text" id="objective" class="form-control select2"/>
                                            <div id="objectiveError" class="error-message"></div>
                                        </div>
                                        <div class="form-group col-sm-2" style="padding-left: 0px">
                                            <button class="btn btn-primary" id="save_corporate_objective"
                                                    onclick="save_corporate_objective.call(this);" type="button">
                                                <?php echo $this->lang->line('common_save'); ?><!--Save & Next--></button>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
            <?php echo footer_page('Right foot', 'Left foot', false); ?>
            <script type="text/javascript">
                app = {};
                app.company_id = <?php echo current_companyID(); ?>;
                app.corporate_objective_table = $('#corporate_objective_table').DataTable({
                    "language": {
                        "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
                    },
                });
                $(document).ready(function () {
                    load_corporate_objective_table(app.company_id);
                });

                function create_corporate_objective_btn_click() {
                    sub_department_form_hide_errors();
                    sub_department_form_reset();
                    app.form_status = 'save';
                    $("#corporate_objective_modal").modal({backdrop: "static"});
                }

                function sub_department_form_reset() {
                    $("#objective").val("");
                }

                function save_corporate_objective() {
                    var objective = $("#objective").val();
                    if (app.form_status == 'save') {
                        if (new_sub_department_form_validation()) {
                            startLoad();
                            $.ajax({
                                dataType: "json",
                                type: "POST",
                                url: "<?php echo site_url('Appraisal/insert_corporate_objective'); ?>",
                                data: {company_id: app.company_id, objective: objective},
                                success: function (data) {
                                    stopLoad();
                                    load_corporate_objective_table(app.company_id);
                                    $("#corporate_objective_modal").modal('hide');
                                    myAlert('s','<?php echo $this->lang->line('appraisal_corporate_Objective_saved_successfully'); ?>')/*Corporate Objective Saved Successfully*/
                                }
                            });
                        }
                    } else if (app.form_status == 'edit') {
                        var corporate_objective_id = app.corporate_objective_id;

                        if (new_sub_department_form_validation()) {
                            startLoad();
                            $.ajax({
                                dataType: "json",
                                type: "POST",
                                url: "<?php echo site_url('Appraisal/update_corporate_objective'); ?>",
                                data: {corporate_objective_id: corporate_objective_id, objective: objective},
                                success: function (data) {
                                    stopLoad();
                                    load_corporate_objective_table(app.company_id);
                                    $("#corporate_objective_modal").modal('hide');
                                    if (data.status == 'success') {
                                        myAlert('s','<?php echo $this->lang->line('appraisal_corporate_Objective_saved_successfully'); ?>');/*Corporate Objective Saved Successfully*/
                                    } else {
                                        myAlert('w','<?php echo $this->lang->line('common_error'); ?>');/*Error*/
                                    }

                                }
                            });
                        }

                    }
                }

                function delete_corporate_objective() {
                    var corporate_objective_id = $(this).data('corporate_objective_id');
                    bootbox.confirm({
                        message: "<?php echo $this->lang->line('appraisal_are_you_sure_you_want_to_delete_the_objective'); ?>",/*Are you sure, you want to delete the Objective?*/
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
                                    url: "<?php echo site_url('Appraisal/delete_corporate_objective'); ?>",
                                    data: {corporate_objective_id: corporate_objective_id},
                                    success: function (data) {
                                        stopLoad();
                                        if(data.status=="already_in_use"){
                                            myAlert('w','This Corporate Objective Already In Use')
                                        }else {
                                            myAlert('s','<?php echo $this->lang->line('appraisal_corporate_objective_deleted_successfully'); ?>')/*Corporate Objective Deleted Successfully*/
                                            load_corporate_objective_table(app.company_id);
                                        }
                                    }
                                });
                            }
                        }
                    });
                }


                function load_corporate_objective_table(company_id) {
                    $.ajax({
                        dataType: "json",
                        type: "POST",
                        url: "<?php echo site_url('Appraisal/get_corporate_objectives'); ?>",
                        data: {company_id: company_id},
                        success: function (data) {
                            app.corporate_objective_table.clear().draw();
                            data.forEach(function (item, index) {
                                let action_buttons = '<div style="text-align: center;"><button class="btn btn-warning btn-xs" style="margin-right: 5px;" data-corporate_objective_description="' + item.corporate_objective_description + '" data-corporate_objective_id="' + item.corporate_objective_id + '" onclick="corporate_objective_edit.call(this)"><i class="fa fa-edit"></i></button>' +
                                    '<button class="btn btn-danger btn-xs" data-corporate_objective_description="' + item.corporate_objective_description + '" data-corporate_objective_id="' + item.corporate_objective_id + '" onclick="delete_corporate_objective.call(this)"><i class="fa fa-trash"></i></button></div>';

                                app.corporate_objective_table.row.add([item.corporate_objective_description, action_buttons]).draw(false);
                            });
                        }
                    });
                }

                function corporate_objective_edit() {
                    sub_department_form_hide_errors();
                    app.form_status = 'edit';
                    app.corporate_objective_id = $(this).data('corporate_objective_id');
                    var corporate_objective_description = $(this).data('corporate_objective_description');
                    $('#objective').val(corporate_objective_description);
                    $('#corporate_objective_modal').modal('show');
                }

                function sub_department_form_hide_errors() {
                    hide_error('objectiveError');
                }

                function new_sub_department_form_validation() {
                    sub_department_form_hide_errors();
                    var objective = $("#objective").val();
                    app.is_valid=true;

                    var status = true;
                    if (objective == "") {
                        show_error('objectiveError', '<?php echo $this->lang->line('common_description_is_required'); ?>')/*Description is required*/
                        app.is_valid = false;
                    }else{
                        let objective_id_for_validate = null;
                        if(app.form_status == 'save'){
                            objective_id_for_validate = -1;//set this to -1 if it is a new record.
                        }else{
                            objective_id_for_validate = app.corporate_objective_id;
                        }
                        $.ajax({
                            async:false,
                            dataType: "json",
                            type: "POST",
                            url: "<?php echo site_url('Appraisal/is_objective_already_exist'); ?>",
                            data: {objective: objective,id:objective_id_for_validate},
                            success: function (data) {
                                if (data.status=='1'){
                                    show_error('objectiveError', '<?php echo $this->lang->line('appraisal_description_already_used'); ?>');/*Description Already Used*/
                                    app.is_valid = false;
                                }
                            }
                        });
                    }
                    return app.is_valid;
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