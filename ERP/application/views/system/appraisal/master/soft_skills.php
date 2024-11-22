<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('appraisal_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('appraisal_master_employee_soft_skills_title');
//echo head_page($title, false);

?>
<style>
    .error-message {
        color: red;
    }

    .act-btn-margin {
        margin: 0 2px;
    }

    .multiselect2.dropdown-toggle {
        width: 100%;
    }
    .btn-group{
        width: 100%;
    }
</style>
<section class="content" id="ajax_body_container">
    <div class="row">
        <div class="col-md-12" id="sub-container">
            <div class="box">
                <div class="box-header with-border" id="box-header-with-border">
                    <h3 class="box-title"
                        id="box-header-title"><?php echo $this->lang->line('appraisal_master_employee_soft_skills_title'); ?></h3>
                    <div class="box-tools pull-right">

                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12" id="sub-container">
                            <div class="row" style=" margin-right: 1px">
                                <div class="col-md-9">
                                    &nbsp;
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-primary pull-right" onclick="template_create_popup_btn()">
                                        <?php echo $this->lang->line('appraisal_add_new_soft_skills_template'); ?>
                                        <!--Add new soft skills template-->
                                    </button>
                                </div>
                            </div>
                            <div class="row" style="margin-top: 10px">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table id="soft_skills_table" class="<?php echo table_class(); ?>">
                                            <thead>
                                            <tr>
                                                <th style="min-width: 15%">
                                                    <?php echo $this->lang->line('appraisal_softskills_name'); ?><!--BSI Code--></th>
                                                <th style="min-width: 15%">
                                                    <?php echo $this->lang->line('appraisal_master_subdepartment_actions_column'); ?><!--Total Value--></th>

                                            </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="create_new_template_modal" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-lg" style="width:33%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"
                    id="CommonEdit_Title"><?php echo $this->lang->line('appraisal_create_soft_skills_template'); ?></h4>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <label for="markingType">Marking Type</label>
                        <?php echo form_dropdown('markingType', array('1' => 'Grade', '2' => 'MPO', '3' => 'MPO Text'), 1, 'class="form-control" id="markingType" required '); ?>
                    </div>
                </div>
                <div class="row" style="margin-top:10px;">
                    <div class="col-md-12">
                        <label for="template_name">Template Name</label>
                        <input id="template_name" class="form-control"/>
                        <div id="template_name_error" class="error-message"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button style="float: right; margin-top: 5px;" class="btn btn-primary"
                                onclick="save_new_template.call(this)">
                            <?php echo $this->lang->line('common_save'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="add_designation_model" class="modal fade" role="dialog">
    <div class="modal-dialog" style="width: 40%">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add Designation</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-3">
                        <label>Template</label>
                    </div>
                    <div class="col-md-9">
                        <div class="form-group">
                            <input class="form-control" id="template_name_designation_modal" disabled="disabled"/>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <label>Designations</label>
                    </div>
                    <div class="col-md-9">
                        <div class="form-group">
                            <select id="template_designations_dropdown" multiple="multiple" class="form-control">
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                    <div class="panel panel-primary" style="margin-top:20px;">
                        <div class="panel-heading">
                            <h3 class="panel-title">Selected Designations</h3>
                        </div>
                        <div class="panel-body" id="selected_designations">
                            No designations selected.
                        </div>
                    </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="save_template_designation()">Save</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script>
    app = {};
    app.company_id = <?php echo current_companyID(); ?>;
    app.softskills_designation_policy = <?php echo softskills_designation_policy(); ?>;
    app.soft_skills_templates_table = $('#soft_skills_table').DataTable({
        "language": {
            "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
        },
    });


    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/appraisal/master/soft_skills', '', '<?php echo $title ?>');
        });
        load_soft_skills_templates(app.company_id);
        load_designations();
    });

    function template_create_popup_btn() {
        $("#template_name").val("");
        hide_error('template_name_error');
        $("#create_new_template_modal").modal({backdrop: 'static'});
    }

    function load_soft_skills_templates(company_id) {
        startLoad();
        app.soft_skills_templates_table.clear().draw();
        $.ajax({
            dataType: "json",
            type: "POST",
            url: "<?php echo site_url('Appraisal/get_softskills_templates'); ?>",
            data: {

                company_id: company_id

            },
            success: function (data) {
                data.forEach(function (item, index) {
                    let action = '<div style="text-align: center;">';
                    if(app.softskills_designation_policy=='1'){
                        action +='<i onclick="open_designation_modal.call(this)" data-id="' + item.id + '" data-name="' + item.name + '" class="fa fa-user-plus act-btn-margin" aria-hidden="true" title="Designation" style="color: #3c8dbc;"></i>';
                    }
                    action += '<i title="Edit" onclick="navigate_to_template_edit_screen.call(this)" data-id="' + item.id + '" data-mid="' + item.markingType + '" class="glyphicon glyphicon-pencil corporate-goal-edit act-btn-margin" style="color: #3c8dbc;"></i>' +
                        '<i title="Delete" onclick="soft_skills_template_delete.call(this)" data-id="' + item.id + '" class="glyphicon glyphicon-trash act-btn-margin btn-task-delete" style="color: #ff3f3a;"></i></div>';
                    app.soft_skills_templates_table.row.add([item.name, action]).draw(false);
                });
                stopLoad();
            }
        });
    }

    function save_template_designation() {
        let selected_designations = $("#template_designations_dropdown").val();
        $.ajax({
            dataType: "json",
            type: "POST",
            url: "<?php echo site_url('Appraisal/save_template_designation'); ?>",
            data: {selected_designations: selected_designations, template_id: app.template_id},
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data.status == 'success') {
                    myAlert('s', data.message);
                    load_selected_designations(app.template_id);
                } else {
                    myAlert('e', data.message);
                }
            },
            error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function open_designation_modal() {
        app.template_id = $(this).data('id');
        let name = $(this).data('name');
        $("#template_name_designation_modal").val(name);
        load_selected_designations(app.template_id);
        $("#add_designation_model").modal('show');
    }

    function load_selected_designations(template_id) {
        $.ajax({
            dataType: "json",
            type: "POST",
            url: "<?php echo site_url('Appraisal/load_selected_designations'); ?>",
            data: {template_id: template_id},
            success: function (data) {
                $('#template_designations_dropdown').val(data.selected_id_array);
                $('#template_designations_dropdown').multiselect2("refresh");
                let selected_designations = "";
                data.selected_designations.forEach(function(item, index){
                    selected_designations+=item.DesDescription+", ";
                });
                selected_designations=selected_designations.slice(0, -2);
                $("#selected_designations").html(selected_designations);

                if(selected_designations==""){
                    $("#selected_designations").html("No designations selected.");
                }
            }
        });
    }

    function load_designations() {
        $.ajax({
            dataType: "json",
            type: "POST",
            url: "<?php echo site_url('Appraisal/designations'); ?>",
            data: {},
            success: function (data) {
                var options = "";
                data.forEach(function (item, index) {
                    options += '<option value="' + item.DesignationID + '">' + item.DesDescription + '</option>';
                })
                $("#template_designations_dropdown").html(options);
                $("#template_designations_dropdown").multiselect2({
                    enableCaseInsensitiveFiltering: true,
                    filterPlaceholder: 'Search Cashier',
                    //includeSelectAllOption: true,
                    maxHeight: 400
                });
                //$("#template_designations_dropdown").multiselect2('selectAll', false);
                $("#template_designations_dropdown").multiselect2('updateButtonText');
            }
        });
    }

    function soft_skills_template_delete() {
        var template_id = $(this).data('id');
        bootbox.confirm({
            message: "<?php echo $this->lang->line('appraisal_are_you_sure_you_want_to_delete_the_template'); ?>",/*Are you sure, you want to delete the template?*/
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
                        url: "<?php echo site_url('Appraisal/delete_soft_skills_template'); ?>",
                        data: {
                            template_id: template_id
                        },
                        success: function (data) {
                            if (data.status == 'success') {
                                load_soft_skills_templates(app.company_id);
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

    function validate_template_form() {
        var is_valid = true;
        var template_name = $("#template_name").val();
        if (template_name == "") {
            is_valid = false;
            show_error('template_name_error', '<?php echo $this->lang->line('common_name_is_required'); ?>.');/*Name is Required*/
        } else {
            $.ajax({
                async: false,
                dataType: "text",
                type: "POST",
                url: "<?php echo site_url('Appraisal/is_template_exist'); ?>",
                data: {template_name: template_name},
                success: function (data) {
                    if (data == 'true') {
                        is_valid = false;
                        show_error('template_name_error', '<?php echo $this->lang->line('appraisal_template_name_already_used'); ?>.');/*Template Name Already Used*/
                    } else {
                        hide_error('template_name_error');
                    }
                }
            });
        }
        return is_valid;
    }

    function save_new_template() {

        var markingType = $("#markingType").val();
        var template_name = $("#template_name").val();
        if (validate_template_form()) {
            startLoad();
            $.ajax({
                dataType: "json",
                type: "POST",
                url: "<?php echo site_url('Appraisal/insert_softskills_template'); ?>",
                data: {

                    company_id: app.company_id,
                    template_name: template_name,
                    markingType : markingType
                },
                success: function (data) {
                    stopLoad();
                    myAlert('s', '<?php echo $this->lang->line('appraisal_successfully_created_the_template')?>.');/*Successfully created the template*/
                    $("#create_new_template_modal").modal('hide');
                    load_soft_skills_templates(app.company_id);
                }
            });
        }

    }

    function navigate_to_template_edit_screen() {
        var template_id = $(this).data('id');
        //saving variable to use in next screen
        localStorage.setItem('softskills_template_id', template_id);
        fetchPage('system/appraisal/master/edit_softskills_template', '0', 'Soft Skills');
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
