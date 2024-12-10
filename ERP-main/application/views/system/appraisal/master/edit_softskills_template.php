<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('appraisal_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('appraisal_master_employee_soft_skills_title');
?>
<style>
    .error-message {
        color: red;
    }

    .act-btn-margin {
        margin: 0 2px;
    }
    .sub-item-order{
        padding-right: 5px;
        font-weight: bold;
    }
    .tbl-mp tbody td {
        font-size: 14px !important;
        padding: 5px 10px;
    }
</style>

<div class="row">
    <div class="col-md-12" id="sub-container">
        <div class="box">
            <div class="box-header with-border" id="box-header-with-border">
                <h3 class="box-title" id="box-header-title"><?php echo $title; ?></h3>
                <div class="box-tools pull-right">
                    <button id="" class="btn btn-box-tool headerclose navdisabl" type="button"><i
                                class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label><?php echo $this->lang->line('appraisal_template_name') ?><!--Template Name--></label>
                        <div><input id="template_name" disabled="disabled" type="text" class="form-control"
                                    style="width: 75%;    display: inline;"/>
                            <button class="btn btn-primary" style="margin-left: 8px;" id="name_edit_button"
                                    onclick="enable_name_for_edit()">
                                <?php echo $this->lang->line('common_edit') ?><!--Edit-->
                            </button>
                        </div>
                        <div id="template-name-error" class="error-message"></div>
                    </div>
                    <div class="form-group col-md-1">
                    </div>
                </div>

                <div class="row" id="MPOField">
                    <div class="form-group col-md-12">
                        <div class="box box-default box-solid">
                            <div class="box-body">

                                    <!--<div class="row">
                                        <div class="row col-sm-12" style="text-align:center;">
                                            <h4><b>JOB DESCRIPTION/ ASSESSMENT</b></h4>
                                        </div>
                                    </div>
                                    <br>&nbsp;</br>-->
                                    <!-- purpose -->
                                    <!--<div class="col-sm-12 mpo_based" style="width:100%;margin-left:0px;padding-left:0px;">
                                        <div class="box box-default box-solid" style="width:100%;padding-left:0px;padding-bottom:0px;padding-top:0px;">
                                            <h4><b><u>JOB PURPOSE/ MAIN FUNCTIONS/ TARGETS</u></b></h3>
                                            <textarea id="job_purpose" class="manager_comment_text" style="width:100%;" onchange="save_jobPurpose(this)"></textarea>
                                        </div>
                                    </div>
                                    -->
                                    
                                    <?php $markingTypee = 3; 

                                    if($markingTypee == 3){ ?>
                                    <!-- MPO start-->
                                    <div class="row">
                                        <div class="row col-sm-12" style="text-align:center;">
                                            <h4><b>MEASURED PERFORMANCE OBJECTIVES </b></h4>
                                        </div>
                                    </div>
                                    <br>&nbsp;</br>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <table class="tbl-mp">
                                                <colgroup>
                                                    <col style="width: 50%; height:30px">
                                                    <col style="width: 50%; height:30px">
                                                </colgroup>
                                                <tr>
                                                    <th colspan="4" style="background: #efefef;padding: 5px 10px;">EMPLOYEE INFORMATION</th>
                                                </tr>
                                                <tr>
                                                    <td>Employee Name: <span> </span></td>
                                                    <td>Employee No.: <span> </span></td>
                                                </tr>
                                                <tr>
                                                    <td>Job Title : <span> </span></td>
                                                    <td>Date of Review: <span> </span></td>
                                                </tr>
                                                <tr>
                                                    <td>Department : <span> </span></td>
                                                    <td>Reporting to : <span> </span></td>
                                                </tr>
                                                <tr>
                                                    <td>Dept. Manager : <span> </span></td>
                                                    <td>Review Period : <span> </span></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4" style="height:10px"> &nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <th colspan="4" style="text-align:center">MPO GUIDELINES</th>
                                                </tr>
                                                <tr>
                                                    <td>a. All MPO’s should be Specific, Measurable, Achievable, Recorded, Time-Bound</td>
                                                </tr>
                                                <tr>
                                                    <td>b. Employee should be given at least three (3) maximum of five (5) MPO’s</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                    <!-- MPO end -->

                                    <?php } ?>
                                    
                                    <h4><u><b>LIST OF MEASURED DUTIES TO BE PERFORMED (MAJOR TASK)</b></u></h4>
                                    <!-- table -->
                                    <div id="mpo_performanceareaTB"></div>
                                
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row" id="gradeField">
                    <div class="form-group col-md-12">
                        <div class="box box-success box-solid">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?php echo $this->lang->line('common_grades') ?><!--Grades--></h3>

                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <div class="table-responsive">
                                    *
                                    <?php echo $this->lang->line('appraisal_top_row_is_highest_grade') ?><!--Top row is highest grade-->
                                    .
                                    <table id="soft_skill_grades_table" class="<?php echo table_class(); ?>">
                                        <thead>
                                        <tr>
                                            <th style="min-width: 15%">
                                                <?php echo $this->lang->line('appraisal_precedence') ?><!--Precedence-->
                                            </th>
                                            <th style="min-width: 15%">
                                                <?php echo $this->lang->line('common_grade') ?><!--Grade-->
                                            </th>
                                            <th style="min-width: 15%">
                                                <?php echo $this->lang->line('common_marks') ?><!--Marks-->
                                            </th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-3">
                                        <label>
                                        Number of Rows<!--Number of Columns-->
                                            : </label> <input id="number_of_columns" type="text"
                                                              style="width: 30px"/>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-3">
                                        <button type="button" class="btn btn-primary" id="" onclick="generate_grades()">
                                        Generate Rows<!--Generate Columns-->
                                        </button>
                                        <button type="button" class="btn btn-danger" id="" onclick="reset_grades()">
                                            <?php echo $this->lang->line('common_reset') ?><!--Reset-->
                                        </button>
                                    </div>
                                    <div class="form-group col-md-3">

                                    </div>
                                    <div class="form-group col-md-5">

                                    </div>
                                    <div class="form-group col-md-1">
                                        <button type="button" class="btn btn-success" id="" onclick="save_grades()">
                                            <?php echo $this->lang->line('common_save') ?><!--Save-->
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!-- /.box-body -->
                        </div>
                    </div>
                </div>

                <div class="row" id="performanceAreasField">
                    <div class="form-group col-md-12">
                        <div class="box box-warning box-solid">
                            <div class="box-header with-border">
                                <h3 class="box-title">
                                    <?php echo $this->lang->line('appraisal_performance_areas') ?><!--Performance Areas--></h3>

                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
<!--                                <div class="table-responsive">-->
<!--                                    <table id="soft_skill_performance_areas" class="--><?php //echo table_class(); ?><!--">-->
<!--                                        <thead>-->
<!--                                        <tr>-->
<!--                                            <th style="min-width: 15%">-->
<!--                                                --><?php //echo $this->lang->line('appraisal_order') ?>
<!--                                            </th>-->
<!--                                            <th style="min-width: 15%">-->
<!--                                                --><?php //echo $this->lang->line('appraisal_performance_area') ?><!--</th>-->
<!--                                            <th style="min-width: 15%">-->
<!--                                                --><?php //echo $this->lang->line('common_action') ?>
<!--                                            </th>-->
<!--                                        </tr>-->
<!--                                        </thead>-->
<!--                                    </table>-->
<!--                                </div>-->

                                <div class="table-responsive">
                                <table id="customer_table_for_nested_items" class="<?php echo table_class(); ?>">
                                    <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('appraisal_order') ?><!--Order--></th>
                                        <th><?php echo $this->lang->line('appraisal_performance_area') ?><!--Performance Area--></th></th>
                                        <th> <?php echo $this->lang->line('common_action') ?><!--Action--></th>
                                    </tr>
                                    </thead>
                                    <tbody id="customer_tablebody_for_nested_items">
                                    </tbody>
                                </table>
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <button type="button" class="btn btn-primary"
                                                    id="add_performance_area_popup_button"
                                                    onclick="add_performance_button_click()">+
                                                <?php echo $this->lang->line('common_add') ?><!--Add-->
                                            </button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <!-- /.box-body -->
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="add_performance_area_popup" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-lg" style="width:33%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="CommonEdit_Title">
                    <?php echo $this->lang->line('appraisal_create_performance_area') ?><!--Create Performance Area--></h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="form-group col-md-12">
                        <label>
                            <?php echo $this->lang->line('appraisal_performance_area') ?><!--Performance Area--></label>
                        <input id="performance_area" class="form-control"/>
                        <div id="performance_area_error" class="error-message"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label><?php echo $this->lang->line('appraisal_order') ?><!--Order--></label>
                        <input id="order" class="form-control" style="width: 100px;"/>
                        <div id="order_error" class="error-message"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group col-md-10">&nbsp;</div>
                        <div class="form-group col-md-2">
                            <button class="btn btn-primary" id="save_performance_area"
                                    onclick="save_performance_area()">
                                <?php echo $this->lang->line('common_save') ?><!--Save-->
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="add_nested_performance_area_popup" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-lg" style="width:33%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"
                    id="CommonEdit_Title"><?php echo $this->lang->line('add_sub_pa')?>
                    </h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="form-group col-md-12">
                        <label><?php echo $this->lang->line('sub_pa')?>
                            </label>
                        <input id="sub_performance_area" class="form-control"/>
                        <div id="sub_performance_area_error" class="error-message"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label><?php echo $this->lang->line('appraisal_order') ?><!--Order--></label>
                        <input id="sub_order" class="form-control" style="width: 100px;"/>
                        <div id="sub_order_error" class="error-message"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group col-md-10">&nbsp;</div>
                        <div class="form-group col-md-2">
                            <button class="btn btn-primary"
                                    onclick="save_nested_performance_area()">
                                <?php echo $this->lang->line('common_save') ?><!--Save-->
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>

<script>
    app = {};
    app.pa_jdtable_status = '';
    app.form_status = null;
    app.company_id = <?php echo current_companyID(); ?>;
    app.soft_skill_grades_table = $('#soft_skill_grades_table').DataTable({
        "language": {
            "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
        },
        "ordering": false
    });
    app.soft_skill_performance_areas = $('#soft_skill_performance_areas').DataTable({
        "language": {
            "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
        },
    });
    app.template_id = localStorage.getItem('softskills_template_id');
    app.template_mid = localStorage.getItem('softskills_template_mid');
    app.generated_grades = new Array();
    app.name_change_button_status = "edit";
    app.maximum_number_of_performance_areas = null;

    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/appraisal/master/soft_skills', '', '<?php echo $title ?>');
        });
        load_soft_skills_template_details(app.template_id);
        //$('#customer_table_for_nested_items').DataTable();
    });

    function save_nested_performance_area() {
        if (app.sub_form_status == 'save') {
            if (validate_subpa_form()) {
                var performance_area = $("#sub_performance_area").val();
                var order = $("#sub_order").val();
                $.ajax({
                    dataType: "json",
                    type: "POST",
                    url: "<?php echo site_url('Appraisal/insert_sub_performance_area'); ?>",
                    data: {
                        template_id: app.template_id,
                        parent_id: app.parent_id,
                        performance_area: performance_area,
                        order: order
                    },
                    success: function (data) {
                        $("#add_nested_performance_area_popup").modal('hide');
                        myAlert('s', data.message);/*Successfully Saved*/
                        load_soft_skills_template_details(app.template_id);
                    }
                });
            }
        } else if (app.sub_form_status == 'edit') {
            if (validate_subpa_form()) {
                var sub_performance_area = $("#sub_performance_area").val();
                var sub_order = $("#sub_order").val();
                $.ajax({
                    dataType: "json",
                    type: "POST",
                    url: "<?php echo site_url('Appraisal/update_sub_performance_area'); ?>",
                    data: {
                        performance_area_id: app.sub_performance_area_id,
                        performance_area: sub_performance_area,
                        order: sub_order
                    },
                    success: function (data) {
                        $("#add_nested_performance_area_popup").modal('hide');
                        myAlert('s', data.message);/*Successfully Modified*/
                        load_soft_skills_template_details(app.template_id);
                    }
                });
            }
        }
    }

    function enable_name_for_edit() {

        if (is_already_using()) {
            myAlert('e', '<?php echo $this->lang->line('appraisal_cannot_do_changes_in_template_the_template_already_in_use'); ?>.');
            return;
        }

        if (app.name_change_button_status == 'edit') {
            $('#name_edit_button').removeClass('btn-primary');
            $('#name_edit_button').addClass('btn-success');
            $('#name_edit_button').text('<?php echo $this->lang->line('common_save'); ?>');/*Save*/
            $('#template_name').removeAttr('disabled', '');
            app.name_change_button_status = 'save';
        } else if (app.name_change_button_status == 'save') {
            if (validate_name_field()) {
                var name = $('#template_name').val();
                $.ajax({
                    dataType: "json",
                    type: "POST",
                    url: "<?php echo site_url('Appraisal/edit_template_name'); ?>",
                    data: {
                        template_id: app.template_id,
                        template_name: name
                    },
                    success: function (data) {
                        if (data.status == 'success') {
                            $('#name_edit_button').removeClass('btn-success');
                            $('#name_edit_button').addClass('btn-primary');
                            $('#name_edit_button').text('<?php echo $this->lang->line('common_edit'); ?>');/*Edit*/
                            $('#template_name').attr('disabled', 'disabled');
                            app.name_change_button_status = 'edit';
                            app.template_name = name;
                            myAlert('s', data.message);
                        }
                    }
                });
            } else {

            }
        }
    }

    function validate_name_field() {
        var is_valid = true;
        var name = $('#template_name').val();
        if (name.replace(/\s/g, "") == "") {
            is_valid = false;
            show_error('template-name-error', '<?php echo $this->lang->line('common_name_is_required'); ?>');/*Name is required*/

        } else {
            if (name != app.template_name) {
                $.ajax({
                    async: false,
                    dataType: "text",
                    type: "POST",
                    url: "<?php echo site_url('Appraisal/is_template_exist'); ?>",
                    data: {template_name: name},
                    success: function (data) {
                        if (data == 'true') {
                            is_valid = false;
                            show_error('template-name-error', '<?php echo $this->lang->line('appraisal_template_name_already_used'); ?>.');/*Template Name Already Used*/
                        } else {
                            hide_error('template-name-error');
                        }
                    }
                });
            } else {
                hide_error('template-name-error');
            }
        }
        return is_valid;
    }


    function validate_subpa_form() {

        var is_valid = true;
        var performance_area = $("#sub_performance_area").val();
        var order = $("#sub_order").val();

        if (performance_area.replace(/\s/g, "") == "") {
            is_valid = false;
            show_error('sub_performance_area_error', '<?php echo $this->lang->line('sub_performance_area_required'); ?>');/*Performance Area is Required*/
        } else {
            hide_error('performance_area_error');
        }

        //if (order == "") {
        //    is_valid = false;
        //    show_error('sub_order_error', 'Order is Required<?php ////echo $this->lang->line('appraisal_order_is_required'); ?>//');/*Order is Required*/
        //} else {
        //    hide_error('sub_order_error');
        //}

        if (order.replace(/\s/g, "") == "") {
            is_valid = false;
            show_error('order_error', '<?php echo $this->lang->line('appraisal_order_is_required'); ?>');/*Order is Required*/
        } else {
            if (app.sub_form_status == 'edit' && app.initial_value_of_suborder_field != order) {
                let index_exist = is_subindex_exist(app.parent_id, order);
                if(index_exist==true){
                    is_valid=false;
                    show_error('sub_order_error', '<?php echo $this->lang->line('index_is_already_exist'); ?>');/*Order is Required*/
                }else{
                    hide_error('sub_order_error');
                }
            } else {
                hide_error('order_error');
            }

        }

        return is_valid;
    }

    function is_subindex_exist(parent_id, order) {
        app.is_valid = true;
        $.ajax({
            async: false,
            dataType: "json",
            type: "POST",
            url: "<?php echo site_url('Appraisal/is_subindex_exist'); ?>",
            data: {
                parent_id: parent_id,
                order: order
            },
            success: function (data) {
                if (data == false) {
                    app.is_valid = false;
                }
            }
        });
        return app.is_valid;
    }

    function is_index_exist(template_id, order) {
        app.is_valid = true;
        $.ajax({
            async: false,
            dataType: "json",
            type: "POST",
            url: "<?php echo site_url('Appraisal/is_index_exist'); ?>",
            data: {
                template_id: template_id,
                order: order
            },
            success: function (data) {
                if (data == false) {
                    app.is_valid = false;
                }
            }
        });
        return app.is_valid;
    }

    function validate_pa_form() {

        var is_valid = true;
        var performance_area = $("#performance_area").val();
        var order = $("#order").val();

        if (performance_area.replace(/\s/g, "") == "") {
            is_valid = false;
            show_error('performance_area_error', '<?php echo $this->lang->line('appraisal_performance_area_is_required'); ?>');/*Performance Area is Required*/
        } else {
            hide_error('performance_area_error');
        }

        if (order.replace(/\s/g, "") == "") {
            is_valid = false;
            show_error('order_error', '<?php echo $this->lang->line('appraisal_order_is_required'); ?>');/*Order is Required*/
        } else {
            if (app.form_status == 'edit' && app.initial_value_of_order_field != order) {
                let index_exist = is_index_exist(app.template_id, order);
                if(index_exist==true){
                    is_valid=false;
                    show_error('order_error', '<?php echo $this->lang->line('index_is_already_exist'); ?>');/*Order is Required*/
                }else{
                    hide_error('order_error');
                }
            } else {
                hide_error('order_error');
            }

        }


        return is_valid;

    }




    function save_performance_area() {
        if (app.form_status == 'save') {
            if (validate_pa_form()) {
                var performance_area = $("#performance_area").val();
                var order = $("#order").val();
                $.ajax({
                    dataType: "json",
                    type: "POST",
                    url: "<?php echo site_url('Appraisal/insert_performance_area'); ?>",
                    data: {
                        template_id: app.template_id,
                        performance_area: performance_area,
                        order: order
                    },
                    success: function (data) {
                        $("#add_performance_area_popup").modal('hide');
                        myAlert('s', '<?php echo $this->lang->line('appraisal_successfully_saved'); ?>');/*Successfully Saved*/
                        load_soft_skills_template_details(app.template_id);
                    }
                });
            }
        } else if (app.form_status == 'edit') {
            if (validate_pa_form()) {
                var performance_area = $("#performance_area").val();
                var order = $("#order").val();
                $.ajax({
                    dataType: "json",
                    type: "POST",
                    url: "<?php echo site_url('Appraisal/update_performance_area'); ?>",
                    data: {
                        performance_area_id: app.performance_area_id,
                        performance_area: performance_area,
                        order: order
                    },
                    success: function (data) {
                        $("#add_performance_area_popup").modal('hide');
                        myAlert('s', '<?php echo $this->lang->line('appraisal_successfully_modified'); ?>');/*Successfully Modified*/
                        load_soft_skills_template_details(app.template_id);
                    }
                });
            }
        }

    }

    function add_performance_button_click() {

        if (is_already_using()) {
            myAlert('e', '<?php echo $this->lang->line('appraisal_cannot_do_changes_in_template_the_template_already_in_use'); ?>.');
            return;
        }

        app.form_status = 'save';
        var next_number = get_next_number_for_pa();
        $("#performance_area").val("");
        $("#add_performance_area_popup").modal('show');
        $("#order").prop('disabled', true);
        hide_error('performance_area_error');
    }

    function get_next_number_for_pa() {
        $.ajax({
            dataType: "json",
            type: "POST",
            url: "<?php echo site_url('Appraisal/get_next_number_for_pa'); ?>",
            data: {
                template_id: app.template_id
            },
            success: function (data) {
                var next_number = data.next_number;
                $("#order").val(next_number);
            }
        });
    }

    function get_next_number_for_subpa(parent_id) {
        $.ajax({
            dataType: "json",
            type: "POST",
            url: "<?php echo site_url('Appraisal/get_next_number_for_subpa'); ?>",
            data: {
                template_id: app.template_id,
                parent_id: parent_id
            },
            success: function (data) {
                var next_number = data.next_number;
                $("#sub_order").val(next_number);
            }
        });
    }

    function generate_grades() {

        if (is_already_using()) {
            myAlert('e', '<?php echo $this->lang->line('appraisal_cannot_do_changes_in_template_the_template_already_in_use'); ?>.');
            return;
        }

        bootbox.confirm({
            message: "<?php echo $this->lang->line('appraisal_existing_grades_will_be_deleted_by_this_action'); ?>",/*Existing grades will be deleted by this action. Some performance areas will be deleted to maintain maximum marks as 100. do you want to proceed?*/
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
                    var number_of_columns = parseInt($('#number_of_columns').val());
                    if (!isNaN(number_of_columns)) {
                        app.soft_skill_grades_table.clear().draw();
                        var precedence = 1;
                        for (var i = 0; i < number_of_columns; i++) {
                            var grade_input = '<input id="grade' + precedence + '" data-precedence="' + precedence + '" onchange="grades_onchange.call(this)" type="text"/>' +
                                '<span id="grade' + precedence + '-error" class="error-message"></span>';
                            var marks_input = '<input id="marks' + precedence + '" data-precedence="' + precedence + '" onchange="marks_onchange.call(this)" type="number"/>' +
                                '<span id="marks' + precedence + '-error" class="error-message"></span>';
                            app.soft_skill_grades_table.row.add([precedence, grade_input, marks_input]).draw(false);
                            app.generated_grades[precedence] = {grade: '', marks: ''};
                            precedence++;
                        }
                    } else {
                        myAlert('e', 'appraisal_numberof_columns_required');/*Number of columns required*/
                    }

                }
            }
        });


    }

    function countInArray(array, what) {
        var count = 0;
        for (var i = 1; i < array.length; i++) {
            if ((array[i].grade).replace(/\s/g, "").toLowerCase() === (what).replace(/\s/g, "").toLowerCase()) {
                count++;
            }
        }
        return count;
    }

    function validate_grades_data() {
        var is_valid = true;
        //get size without index 0
        var array_size = app.generated_grades["length"] - 1;

        if (array_size == '-1') {
            is_valid = false;
        }

        for (var i = 1; i <= array_size; i++) {
            var grade = app.generated_grades[i].grade;
            var marks = app.generated_grades[i].marks;

            if (grade.replace(/\s/g, "") == "") {
                var error_label_selector = '#grade' + i + '-error';
                $(error_label_selector).text('<?php echo $this->lang->line('appraisal_grade_is_required'); ?>');/*Grade is required*/
                is_valid = false;
            } else {
                var occurences = countInArray(app.generated_grades,grade);
                if(occurences>1){
                    var error_label_selector = '#grade' + i + '-error';
                    $(error_label_selector).text('<?php echo $this->lang->line('cannot_duplicate_grades'); ?>');/*Grade is required*/
                    is_valid = false;
                }else{
                    var error_label_selector = '#grade' + i + '-error';
                    $(error_label_selector).text('');
                }

            }

            if (marks.replace(/\s/g, "") == "") {
                var error_label_selector = '#marks' + i + '-error';
                $(error_label_selector).text('<?php echo $this->lang->line('appraisal_should_be_less_than_previous_value'); ?>.');/*should be less than previous value*/
                is_valid = false;
            } else {
                var error_label_selector = '#marks' + i + '-error';
                $(error_label_selector).text('');
                if (i > 1) {
                    var previous_marks = app.generated_grades[i - 1].marks;

                    if (parseInt(previous_marks) > parseInt(marks)) {
                        var error_label_selector = '#marks' + i + '-error';
                        $(error_label_selector).text('');
                        app.generated_grades[i].marks = marks;
                    } else {
                        var error_label_selector = '#marks' + i + '-error';
                        $(error_label_selector).text('<?php echo $this->lang->line('appraisal_should_be_less_than_previous_value'); ?>');/*should be less than previous value*/
                        is_valid = false;
                    }
                }
            }

        }

        return is_valid;

    }

    function save_grades() {

        if (is_already_using()) {
            myAlert('e', '<?php echo $this->lang->line('appraisal_cannot_do_changes_in_template_the_template_already_in_use'); ?>.');
            return;
        }

        if (app.generated_grades.length == 0) {
            myAlert('w', '<?php echo $this->lang->line('appraisal_no_changes_to_save'); ?>');/*No Changes to Save*/
        }

        if (validate_grades_data()) {
            $.ajax({
                dataType: "json",
                type: "POST",
                url: "<?php echo site_url('Appraisal/insert_softskills_grades'); ?>",
                data: {
                    grades_array: app.generated_grades,
                    template_id: app.template_id
                },
                success: function (data) {
                    myAlert('s', '<?php echo $this->lang->line('appraisal_successfully_saved'); ?>');/*Successfully Saved*/
                    app.soft_skill_grades_table.clear().draw();
                    app.generated_grades = [];
                    load_soft_skills_template_details(app.template_id);
                }
            });
        }
    }

    function grades_onchange() {
        var precedence = $(this).data('precedence');
        var grade = $(this).val();
        app.generated_grades[precedence].grade = grade;
    }

    function marks_onchange() {
        var precedence = $(this).data('precedence');
        var marks = $(this).val();
        if (precedence > 1) {
            var previous_marks = app.generated_grades[precedence - 1].marks;
            if (parseInt(previous_marks) > parseInt(marks)) {
                var error_label_selector = '#marks' + precedence + '-error';
                $(error_label_selector).text('');
                app.generated_grades[precedence].marks = marks;
            } else {
                var error_label_selector = '#marks' + precedence + '-error';
                $(error_label_selector).text('<?php echo $this->lang->line('appraisal_should_be_less_than_previous_value'); ?>');/**/
                app.generated_grades[precedence].marks = marks;
            }
        } else {
            app.generated_grades[precedence].marks = marks;
        }
    }

    function load_soft_skills_template_details(template_id) {
        startLoad();
        $("#customer_tablebody_for_nested_items").html("");
        $("#mpo_performanceareaTB").html("");
        $.ajax({
            dataType: "json",
            type: "POST",
            url: "<?php echo site_url('Appraisal/get_softskills_template_details'); ?>",
            data: {
                template_id: template_id
            },
            success: function (data) {
                if(data.markingType['markingType'] == 2){
                    $('#job_purpose').text(data.job_purpose['job_purpose']);
                    $('#MPOField').removeClass('hide');
                    $('#gradeField').addClass('hide');
                }else{
                    $('#MPOField').addClass('hide');
                    $('#gradeField').removeClass('hide');
                }

               //....................................................................skills grades list
                app.soft_skill_grades_table.clear();
                app.is_already_using = data.is_already_using;
                data.skills_grades_list.forEach(function (item, index) {

                    var precedence = item.precedence;
                    var grade = item.grade;
                    var marks = item.marks;
                    if (marks == '-1') {
                        marks = "-";
                    }
                    app.soft_skill_grades_table.row.add([precedence, grade, marks]).draw(false);
                    if (precedence == 1) {
                        app.maximum_number_of_performance_areas = (100 / parseInt(item.marks));
                    }
                });

                //....................................................................MPO skill-performance-area tables
                var grand_total = 0;
                if(data.markingType['markingType'] == 2){
                    var tbl = "";
                    data.skills_performance_area_list.forEach(function (hd_item, index) {

                        var performance_area = hd_item.performance_area;
                        var order = hd_item.order;

                        tbl += ' <div class="table-responsive">';
                            tbl +=' <table id="soft_skill_MPO_table" class="<?php echo table_class(); ?>">';
                                    tbl +=' <thead>';
                                        tbl +=' <tr>';
                                            tbl +=' <th style="min-width: 5%"><b>'+order+'</b></th>';
                                            tbl +=' <th class="text-left" style="min-width: 75%"><u><b>'+performance_area+'</b></u></th>';
                                            tbl +=' <th style="min-width: 15%"><b>Measured Points</b></th>';
                                            tbl +=' <th style="min-width: 5%"><b>Text Answers</b></th>';
                                        tbl +=' </tr>';
                                    tbl +=' </thead>';
                                    tbl +=' <tbody>';

                                    var mpoint = 0;
                                    var total = 0;
                                    hd_item.sub_performance_areas.forEach(function(sub_item, index){

                                        mpoint = sub_item.measuredPoints ? parseFloat(sub_item.measuredPoints) : 0; // Convert to number

                                        tbl +=' <tr>';
                                            tbl +=' <td class="text-center">&nbsp;</td>';
                                            tbl +=' <td>* '+sub_item.performance_area+'</td>';
                                            tbl +=' <td><input type="text" name="measuredPoint" style="width:100%" id="measuredPoint_'+ sub_item.id +'" placeholder="0" class="number" onkeyup="calculate_total_measurePoints(this, '+ hd_item.id  +')" ';
                                            tbl +=' onchange="save_measurepoint('+ sub_item.id +', this.value)" value="'+ mpoint +'"></td> ';
                                            tbl +=' <td><input type="text" name="measuredPointText" style="width:100%" id="measuredPointText_'+ sub_item.id +'" onchange="save_measurepointText('+ sub_item.id +', this.value)" value=""></td>';
                                        tbl +=' </tr>';

                                        total += mpoint;
                                    });

                                        tbl +=' <tr style="background-color:rgb(221,210,0,0.6);">';
                                            tbl +=' <td class="text-center">-</td>';
                                            tbl +=' <td class="text-center">Total</td>';
                                            tbl +=' <td><input type="text" value="'+ total +'" style="width:100%;background-color:rgb(221,210,0,0.6);" id="total_'+ hd_item.id +'" placeholder="0" class="number" readonly></td>';
                                        tbl +=' </tr>';

                                    tbl +=' </tbody>';
                            tbl +=' </table>';
                        tbl +=' </div>';

                        grand_total += total;
                    });
                    var tottbl = "";
                    var percentage = 100 + '%';

                    tottbl += ' <div class="table-responsive">';
                        tottbl +=' <table id="soft_skill_MPO_grandTotal_table" class="<?php echo table_class(); ?>">';
                            tottbl +=' <tbody>';
                                tottbl +=' <tr style="background-color:rgba(255,163,34,0.67);">';
                                    tottbl +=' <td class="text-center" style="min-width: 5%">--</td>';
                                    tottbl +=' <td class="text-center" style="min-width: 80%">Grand Total</td>';
                                    tottbl +=' <td><input type="text" value="'+grand_total+'" style="width:100%;background-color:rgba(255,163,34,0.67);" id="" placeholder="0" class="number" readonly></td>';
                                tottbl +=' </tr>';
                                tottbl +=' <tr style="background-color:rgba(255,163,34,0.67);">';
                                    tottbl +=' <td class="text-center" style="min-width: 5%">%</td>';
                                    tottbl +=' <td class="text-center" style="min-width: 80%">Percentage</td>';
                                    tottbl +=' <td><input type="text" value="'+percentage+'" style="width:100%;background-color:rgba(255,163,34,0.67);" id="" placeholder="0" class="number" readonly></td>';
                                tottbl +=' </tr>';
                            tottbl +=' </tbody>';
                        tottbl +=' </table>';
                    tottbl +=' </div>';

                    tbl += tottbl;

                    $('#mpo_performanceareaTB').html(tbl);

                }

                //.....................................................................skills performance area structure
                var tbody = "";
                data.skills_performance_area_list.forEach(function (item, index) {
                    var performance_area = item.performance_area;
                    var order = item.order;
                    var action = '';
                    action += '<div style="text-align: center;">';
                    action += '<i title="Add sub performance area" onclick="add_nested_performance_area.call(this)" data-id="' + item.id + '" class="glyphicon glyphicon-plus act-btn-margin" style="color: #3c8dbc;"></i>'
                    action += '<i title="Edit performance area" onclick="performance_area_edit_popup.call(this)" data-id="' + item.id + '" class="glyphicon glyphicon-pencil corporate-goal-edit act-btn-margin" style="color: #3c8dbc;"></i>';
                    action += '<i title="Delete performance area" onclick="performance_area_delete.call(this)" data-id="' + item.id + '" class="glyphicon glyphicon-trash act-btn-margin btn-task-delete" style="color: #ff3f3a;"></i></div>';
                    tbody +='<tr>';
                    tbody += '<td>'+order+'</td>';
                    tbody += '<td>';
                    tbody += '<table class="<?php echo table_class(); ?>">';
                    tbody += '<tr><td>'+performance_area+'</td></tr>';
                    let root_item_id = item.id;
                    item.sub_performance_areas.forEach(function(item, index){
                        let sub_action = '<i onclick="sub_performance_area_edit_popup.call(this)" data-parent="' +root_item_id + '" data-id="' + item.id + '" class="glyphicon glyphicon-pencil act-btn-margin" style="color: #3c8dbc;"></i>' +
                            '<i onclick="sub_performance_area_delete.call(this)" data-id="' + item.id + '" class="glyphicon glyphicon-trash act-btn-margin btn-task-delete" style="color: #ff3f3a;"></i></div>';
                        
                        tbody += '<tr><td style="padding-left: 20px;"><span class="sub-item-order">'+item.order+'.</span>'+item.performance_area+'<span style="float: right">'+sub_action+'</span></td></tr>';
                    });
                    tbody += '</table>';
                    tbody += '</td>';
                    tbody += '<td>'+action+'</td>';
                    tbody +='</tr>';
                });

                $("#customer_tablebody_for_nested_items").html(tbody);

                if(app.pa_jdtable_status != 'initialised'){
                    app.pa_table = $('#customer_table_for_nested_items').DataTable({
                        "order": [[ 0, "asc" ]]
                    });
                    app.pa_jdtable_status = 'initialised';
                }
                app.number_of_performance_areas = data.skills_performance_area_list.length;
                if(data.markingType['markingType'] == 2){
                    $('#add_performance_area_popup_button').removeAttr('disabled');
                    $('#add_performance_area_popup_button').attr('title', '');
                }else{
                    if (data.skills_grades_list.length == 0) {
                        $('#add_performance_area_popup_button').attr('disabled', 'disabled');
                        $('#add_performance_area_popup_button').attr('title', '<?php echo $this->lang->line('appraisal_need_to_add_grades_before_adding_performance_areas'); ?>');/*Need to add grades before adding performance areas*/
                    } else {
                        $('#add_performance_area_popup_button').removeAttr('disabled');
                        $('#add_performance_area_popup_button').attr('title', '');
                    }
                }

                $('#template_name').val(data.skills_template_details[0].name);
                app.template_name = data.skills_template_details[0].name;
                stopLoad();
            }
        });
    }
    
    function sub_performance_area_edit_popup() {
        if (is_already_using()) {
            myAlert('e', '<?php echo $this->lang->line('appraisal_cannot_do_changes_in_template_the_template_already_in_use'); ?>.');
            return;
        }

        hide_error('sub_order_error');
        startLoad();
        app.sub_form_status = 'edit';
        var sub_performance_area_id = $(this).data('id');
        app.sub_performance_area_id = sub_performance_area_id;
        app.parent_id = $(this).data('parent');
        $("#sub_order").prop('disabled', false);
        hide_error('sub_performance_area_error');
        $.ajax({
            dataType: "json",
            type: "POST",
            url: "<?php echo site_url('Appraisal/get_performance_area_details'); ?>",
            data: {
                performance_area_id: sub_performance_area_id
            },
            success: function (data) {
                $("#sub_performance_area").val(data[0].performance_area);
                $("#sub_order").val(data[0].order);
                app.initial_value_of_suborder_field = data[0].order;
                $("#add_nested_performance_area_popup").modal('show');
                stopLoad();
            }
        });


    }

    function add_nested_performance_area() {
        $("#sub_performance_area").val("");
        if (is_already_using()) {
            myAlert('e', '<?php echo $this->lang->line('appraisal_cannot_do_changes_in_template_the_template_already_in_use'); ?>.');
            return;
        }
        app.parent_id = $(this).data('id');
        app.sub_form_status = 'save';
        get_next_number_for_subpa(app.parent_id);
        $("#sub_order").attr('disabled', 'disabled');
        $("#add_nested_performance_area_popup").modal('show');
    }

    function sub_performance_area_delete(){
        if (is_already_using()) {
            myAlert('e', '<?php echo $this->lang->line('appraisal_cannot_do_changes_in_template_the_template_already_in_use'); ?>.');
            return;
        }

        var performance_area_id = $(this).data('id');
        bootbox.confirm({
            message: "<?php echo $this->lang->line('appraisal_are_you_sure_you_want_to_delete_the_performance_area'); ?>",/*Are you sure, you want to delete the Performance Area?*/
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
                        url: "<?php echo site_url('Appraisal/delete_performance_area'); ?>",
                        data: {
                            performance_area_id: performance_area_id
                        },
                        success: function (data) {
                            myAlert('s', '<?php echo $this->lang->line('appraisal_successfully_deleted'); ?>');/*Successfully deleted*/
                            //load_soft_skills_template_details(app.template_id);
                            navigate_to_template_edit_screen(app.template_id);
                            stopLoad();
                        }
                    });
                }
            }
        });
    }

    function performance_area_delete() {

        if (is_already_using()) {
            myAlert('e', '<?php echo $this->lang->line('appraisal_cannot_do_changes_in_template_the_template_already_in_use'); ?>.');
            return;
        }

        var performance_area_id = $(this).data('id');
        bootbox.confirm({
            message: "<?php echo $this->lang->line('appraisal_are_you_sure_you_want_to_delete_the_performance_area'); ?>",/*Are you sure, you want to delete the Performance Area?*/
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
                        url: "<?php echo site_url('Appraisal/delete_performance_area'); ?>",
                        data: {
                            performance_area_id: performance_area_id
                        },
                        success: function (data) {
                            myAlert('s', '<?php echo $this->lang->line('appraisal_successfully_deleted'); ?>');/*Successfully deleted*/
                            load_soft_skills_template_details(app.template_id);
                            stopLoad();
                        }
                    });
                }
            }
        });
    }

    function performance_area_edit_popup() {

        if (is_already_using()) {
            myAlert('e', '<?php echo $this->lang->line('appraisal_cannot_do_changes_in_template_the_template_already_in_use'); ?>.');
            return;
        }

        hide_error('order_error');
        startLoad();
        app.form_status = 'edit';
        var performance_area_id = $(this).data('id');
        app.performance_area_id = performance_area_id;
        $("#order").prop('disabled', false);
        hide_error('performance_area_error');
        $.ajax({
            dataType: "json",
            type: "POST",
            url: "<?php echo site_url('Appraisal/get_performance_area_details'); ?>",
            data: {
                performance_area_id: performance_area_id
            },
            success: function (data) {
                $("#performance_area").val(data[0].performance_area);
                $("#order").val(data[0].order);
                app.initial_value_of_order_field = data[0].order;
                $("#add_performance_area_popup").modal('show');
                stopLoad();
            }
        });
    }

    function is_already_using() {
        app.x = false;
        $.ajax({
            async: false,
            dataType: "json",
            type: "POST",
            url: "<?php echo site_url('Appraisal/is_template_already_using'); ?>",
            data: {
                template_id: app.template_id
            },
            success: function (data) {
                app.x = data.status;
            }
        });
        return app.x;
    }

    function reset_grades() {

        if (is_already_using()) {
            myAlert('e', '<?php echo $this->lang->line('appraisal_cannot_do_changes_in_template_the_template_already_in_use'); ?>.');/*Cannot do changes in template, The template already in use*/
            return;
        }

        $("#add_performance_area_popup_button").prop('disabled', true);
        var template_id = app.template_id;
        bootbox.confirm({
            message: "<?php echo $this->lang->line('appraisal_this_action_will_deletes_all_the_grades_in_this_template_Are_you_sure_You_want_to_continue'); ?>",/*This action will deletes all the grades in this template. Are you sure, You want to continue?*/
            buttons: {
                confirm: {
                    label: '<?php echo $this->lang->line('common_yes'); ?>',/*Yes*/
                    className: 'btn-success'
                },
                cancel: {
                    label: '<?php echo $this->lang->line('common_no'); ?>',/*No*/
                    className: 'btn-danger'
                }
            },
            callback: function (user_confirmation) {
                if (user_confirmation) {
                    startLoad();
                    $.ajax({
                        dataType: "json",
                        type: "POST",
                        url: "<?php echo site_url('Appraisal/delete_grades'); ?>",
                        data: {
                            template_id: template_id
                        },
                        success: function (data) {
                            if (data.status == 'success') {
                                app.generated_grades = [];
                                app.soft_skill_grades_table.clear().draw();
                                $("#number_of_columns").val("");
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

    function navigate_to_template_edit_screen(template_id) {
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


        function calculate_total_measurePoints(element, nmbr) {
            var total = 0;

            $(element).closest('table').find('input[name="measuredPoint"]').each(function() {
                total += parseFloat($(this).val()) || 0;
            });

            $('#total_' + nmbr).val(total);
        }

        function save_measurepoint(id, value) {
            var template_id = app.template_id;
            $.ajax({
                type: 'POST',
                dataType: "json",
                url: "<?php echo site_url('Appraisal/save_measurepoint'); ?>",
                data: { template_id: template_id, id: id, value: value },
                beforeSend: function () {
                        startLoad();
                    },
                success: function(data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    load_soft_skills_template_details(template_id);
                },
                error: function(error) {
                    console.error('Error saving data', error);
                }
            });
        }

        function save_measurepointText(id, value) {
            var template_id = app.template_id;
            $.ajax({
                type: 'POST',
                dataType: "json",
                url: "<?php echo site_url('Appraisal/save_measurepointText'); ?>",
                data: { template_id: template_id, id: id, value: value },
                beforeSend: function () {
                        startLoad();
                    },
                success: function(data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    load_soft_skills_template_details(template_id);
                },
                error: function(error) {
                    console.error('Error saving data', error);
                }
            });
        }

        function save_jobPurpose(inputElement){
            var job_purpose = inputElement.value;
            var template_id = app.template_id;
            $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'template_id': template_id, 'job_purpose': job_purpose},
                    url: "<?php echo site_url('Appraisal/update_job_purpose'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0],data[1]);
                        load_soft_skills_template_details(template_id);
                    },
                    error: function () {
                        myAlert('e', 'An Error Occurred! Please Try Again.');
                        stopLoad();
                        refreshNotifications(true);
                    }
            });
        }

</script>
