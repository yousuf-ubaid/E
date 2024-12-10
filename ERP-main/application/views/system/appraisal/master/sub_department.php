<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('appraisal_lang', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('appraisal_master_departments_title');


/*echo head_page('Supplier Invoices', true);*/
$supplier_arr = all_supplier_drop(false);
$date_format_policy = date_format_policy();


?>
<style>
    .error-message {
        color: red;
    }
</style>

<link rel="stylesheet" href="<?php echo base_url('plugins/Horizontal-Hierarchical/src/jquery.hortree.css'); ?>"/>
<script type="text/javascript" src="<?php echo base_url('plugins/treeview/bootstrap-treeview.js'); ?>"></script>

<div class="row">
    <div class="col-md-12" id="sub-container">
        <div class="box">
            <div class="box-header with-border" id="box-header-with-border">
                <h3 class="box-title" id="box-header-title"><?php echo $title; ?></h3>
                <div class="box-tools pull-right">

                </div>
            </div>
            <div class="box-body" style="">
                <div class="col-md-8" id="main-content-div">
                    <div id="tree"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="CommonEdit_Mod" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="CommonEdit_Title">
                    <?php echo $this->lang->line('appraisal_master_subdepartment_column'); ?><!--Sub Department--></h4>
            </div>

            <div class="modal-body">
                <div class="tab-content">
                    <div id="step1" class="tab-pane active">

                        <input type="hidden" id="supplierCreditPeriodhn" name="supplierCreditPeriodhn">
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <!--                Department field -->
                                <label for="invoiceType">
                                    <?php echo $this->lang->line('appraisal_master_create_new_subdepartment_form_field_1'); ?><!--Invoice Type--> <?php required_mark(); ?></label>
                                <input type="text" id="department" class="form-control select2" disabled/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <!--                Sub Department Code field -->
                                <label for="subDepartmentCode">
                                    <?php echo $this->lang->line('appraisal_master_create_new_subdepartment_form_field_3'); ?><!--Invoice Type--> <?php required_mark(); ?></label>
                                <input type="text" id="subDepartmentCode" class="form-control"/>
                                <div id="subDepartmentCodeError" class="error-message"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <!--                Sub Department Description field -->
                                <label for="subDepartmentDescription">
                                    <?php echo $this->lang->line('appraisal_master_create_new_subdepartment_form_field_2'); ?><!--Invoice Type--> <?php required_mark(); ?></label>
                                <input type="text" id="subDepartmentDescription" class="form-control"/>
                                <div id="subDepartmentDescriptionError" class="error-message"></div>
                                <div id="subDepartmentGeneralError" class="error-message"></div>
                            </div>
                        </div>


                    </div>
                    <div id="step2" class="tab-pane">

                    </div>

                </div>


            </div>
            <div class="modal-footer">
                <div class="text-right m-t-xs">
                    <button class="btn btn-primary" id="save_sub_department" type="button">
                        <?php echo $this->lang->line('common_save'); ?><!--Save & Next--></button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="select_hod_modal" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="CommonEdit_Title">
                    <?php echo $this->lang->line('appraisal_head_of_department'); ?><!--Head of Department--></h4>
            </div>
            <div class="modal-body" style="overflow-y: scroll;height: 100px;">
                <div class="row">
                    <div class="form-group col-sm-4">
                        <select class="form-control" id="employee_list">
                        </select>
                        <div id="select_hod_error" class="error-message"></div>
                    </div>
                    <div class="form-group col-sm-4">

                        <button class="btn btn-primary" onclick="save_hod();">
                            <?php echo $this->lang->line('appraisal_save_hod'); ?><!--Save HOD--></button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="update_department_logo_modal" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-lg" style="width:32%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="CommonEdit_Title">
                    Department Logo</h4>
            </div>
            <div class="modal-body" style="overflow-y: scroll;height: 145px;">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="fileinput-new thumbnail" style="height: 100px;width:100px;">
                            <?php if (isset($header['contactImage']) != '') { ?>
                                <img
                                        src="<?php echo base_url('uploads/crm/profileimage/' . isset($header['contactImage'])); ?>"
                                        id="changeImg" style="width: 100px; height: 92px;">
                                <?php
                            } else { ?>
                                <img src="<?php echo base_url('images/item/no-image.png'); ?>" id="changeImg"
                                     style="width: 100px; height: 92px;">
                            <?php } ?>
                            <input type="file" name="contactImage" id="itemImage" style="display: none;"
                                   onchange="loadImage(this)"/>
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
    app.appraisal_master_create_new_subdepartment_btn = "<?php echo $this->lang->line('appraisal_master_create_new_subdepartment_btn'); ?>";

    $('#changeImg').click(function () {
        $('#itemImage').click();
    });

    function loadImage(obj) {
        if (obj.files && obj.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#changeImg').attr('src', e.target.result);
            };
            reader.readAsDataURL(obj.files[0]);
            profileImageUploadContact();
        }
    }

    function profileImageUploadContact() {
        var imgageVal = new FormData();
        imgageVal.append('departmentID', app.department_id);
        var files = $("#itemImage")[0].files[0];
        imgageVal.append('files', files);
        // var formData = new FormData($("#contact_profile_image_uplode_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: imgageVal,
            contentType: false,
            cache: false,
            processData: false,
            url: "<?php echo site_url('Appraisal/upload_department_logo'); ?>",
            beforeSend: function () {
                startLoad();
                $('#itemImage').val('');
            },
            success: function (data) {
                stopLoad();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();

            }
        });
    }


    function save_hod() {
        var hod_id = $('#employee_list').val();

        if (hod_id == 0) {
            show_error('select_hod_error', '<?php echo $this->lang->line('appraisal_please_select_a_value'); ?>');/*Please select a value*/
        } else {
            $.ajax({
                async: false,
                dataType: "json",
                type: "POST",
                url: "<?php echo site_url('Appraisal/set_hod_id_of_a_department'); ?>",
                data: {department_id: app.config_department_id, hod_id: hod_id},
                success: function (data) {
                    $("#select_hod_modal").modal('hide');
                    var message = '<?php echo $this->lang->line('appraisal_hod_of_the_department_has_been_updated'); ?>';/*HOD of the department has been updated.*/
                    //var message = 'HOD of the ' + data.hod_details.DepartmentDes + ' department has updated.';
                    myAlert('s', message);
                    load_sub_departments_table(app.company_id);
                }
            });
        }

    }

    function show_hod_modal() {
        hide_error('select_hod_error');
        app.config_department_id = $(this).data('department_id');
        var hod_id = get_hod_id_of_a_department(app.config_department_id);
        load_department_employees_dropdown(hod_id);
        $("#select_hod_modal").modal('show');
    }

    function get_hod_id_of_a_department(department_id) {
        app.hod_id = null;
        $.ajax({
            async: false,
            dataType: "json",
            type: "POST",
            url: "<?php echo site_url('Appraisal/get_hod_id_of_a_department'); ?>",
            data: {department_id: department_id},
            success: function (data) {
                app.hod_id = data.hod_details.hod_id;
            }
        });
        return app.hod_id;
    }

    function load_department_employees_dropdown(selected_value) {
        $.ajax({
            async: false,
            dataType: "json",
            type: "POST",
            url: "<?php echo site_url('Appraisal/get_department_employees'); ?>",
            data: {department_id: app.config_department_id},
            success: function (data) {
                var employees = "";

                employees += '<option value="0">Select Employee as a HOD</option>';
                data.forEach(function (item, index) {
                    if (selected_value == item.EIdNo) {
                        select_status = "selected";
                    } else {
                        select_status = "";
                    }
                    employees += '<option ' + select_status + ' value="' + item.EIdNo + '">' + item.Ename1 + ' - ' + item.DepartmentDes + '</option>';
                });
                //app.department_employees_drop_down_list_html = employees;
                $('#employee_list').html(employees);
                $("#employee_list").select2({
                    placeholder: 'Select an option',
                    tags: true
                });
            }
        });
    }


    function open_deplogo_modal(department_id,logo) {
        app.department_id = department_id;
        get_department_logo(department_id);
        $("#update_department_logo_modal").modal('show');
    }

    function get_department_logo(department_id){
        $.ajax({
            dataType: "text",
            type: "POST",
            url: "<?php echo site_url('Appraisal/get_department_logo'); ?>",
            data: {department_id: department_id},
            success: function (data) {
                $("#changeImg").attr("src",data);
            }
        });
    }

    // app.sub_department_table = $('#sub_department_table').DataTable();
    function getTree(data) {
        var departments = [];

        data.forEach(function (item, index) {
            var department = {};
            var department_description = item.department_description;
            var hod_name;
            if (item.hod_name != null) {
                hod_name = item.hod_name;
            } else {
                hod_name = "<?php echo $this->lang->line('appraisal_not_selected'); ?>";/*Not selected*/
            }
            department.text = item.department_description + '<button style="float: right;margin-left: 10px;" class="btn btn-success btn-xs" onclick="open_deplogo_modal(\'' + item.department_master_id + '\',\''+item.logo+'\')">Update Department Logo</button><button style="float: right;margin-left: 10px;" data-department_id="' + item.department_master_id + '" onclick="show_hod_modal.call(this)" class="btn btn-success btn-xs btn-add-department"><?php echo $this->lang->line('appraisal_select_hod'); ?></button><button data-department_id="' + item.department_master_id + '" data-department_description="' + item.department_description + '" class="btn btn-success btn-xs btn-add-department" style="position: relative;float: right;" onclick="btn_add_department.call(this)"><i class="fa fa-plus"></i> ' + app.appraisal_master_create_new_subdepartment_btn + '</button> | <?php echo $this->lang->line('appraisal_hod'); ?>: ' + hod_name;
            if (item.sub_departments.length > 0) {
                department.nodes = [];
            }
            department.selectable = false;
            item.sub_departments.forEach(function (item, index) {

                var sub_department = {};
                sub_department.text = item.sub_department_code + ' - ' + item.sub_department_description + '<button data-department_description="' + department_description + '" data-sub_department_code="' + item.sub_department_code + '" data-sub_department_id="' + item.sub_department_id + '" data-sub_department_description="' + item.sub_department_description + '" class="btn btn-danger btn-xs sub-department-delete" style="position: relative;float: right;" onclick="sub_department_delete.call(this)"><i class="fa fa-trash-o"></i></button><button data-sub_department_code="' + item.sub_department_code + '" data-department_description="' + department_description + '" data-sub_department_id="' + item.sub_department_id + '" data-sub_department_description="' + item.sub_department_description + '" class="btn btn-warning btn-xs sub-department-edit" style="position: relative;float: right;    margin-right: 2px;" onclick="sub_department_edit.call(this)"><i class="fa fa-edit"></i></button>';
                sub_department.selectable = false;
                department.nodes.push(sub_department);

            });
            departments.push(department);
        });
        return departments;
    }

    $(document).ready(function () {
        load_sub_departments_table(app.company_id);
        //load_department_dropdown(app.company_id);
    });

    function load_sub_departments_table(company_id) {

        $.ajax({
            dataType: "json",
            type: "POST",
            url: "<?php echo site_url('Appraisal/get_departments'); ?>",
            data: {company_id: company_id},
            success: function (data) {
                $('#tree').treeview({data: getTree(data)});
            }
        });
    }

    function sub_department_edit() {
        sub_department_form_hide_errors();
        app.form_status = 'edit';
        app.sub_department_id = $(this).data('sub_department_id');
        var sub_department_description = $(this).data('sub_department_description');
        var department_description = $(this).data('department_description');
        var sub_department_code = $(this).data('sub_department_code');

        $("#department").val(department_description);
        $("#subDepartmentDescription").val(sub_department_description);
        $("#subDepartmentCode").val(sub_department_code);

        $("#CommonEdit_Mod").modal('show');
    }

    function sub_department_delete() {
        app.sub_department_id = $(this).data('sub_department_id');
        bootbox.confirm({
            message: "<?php echo $this->lang->line('appraisal_are_you_sure_you_want_to_delete_this_sub_department'); ?>",/*Are you sure you want to delete this sub department?*/
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
                        url: "<?php echo site_url('Appraisal/delete_sub_departments'); ?>",
                        data: {sub_department_id: app.sub_department_id},
                        success: function (data) {
                            stopLoad();
                            if (data.status == "already_in_use") {
                                myAlert('w', '<?php echo $this->lang->line('appraisal_item_already_in_use'); ?>');/*Item already in use*/
                            } else if (data.status == "success") {
                                myAlert('s', '<?php echo $this->lang->line('appraisal_sub_department_deleted_successfully'); ?>');/*Sub Department Deleted Successfully*/
                                load_sub_departments_table(app.company_id);
                            }
                        }
                    });
                }
            }
        });


    }


    function btn_add_department() {
        app.form_status = 'save';
        sub_department_form_hide_errors();
        clear_form();
        app.department_id = $(this).data('department_id');

        department_description = $(this).data('department_description');
        $("#department").val(department_description);
        $("#CommonEdit_Mod").modal('show');
    }

    function clear_form() {
        $("#subDepartmentDescription").val("");
        $("#subDepartmentCode").val("");
    }


    $("#save_sub_department").click(function () {


        if (app.form_status == 'save') {
            if (new_sub_department_form_validation()) {
                startLoad();
                var selected_department_id = app.department_id;
                var sub_department_description = $("#subDepartmentDescription").val();
                var sub_department_code = $("#subDepartmentCode").val();
                $.ajax({
                    dataType: "json",
                    type: "POST",
                    url: "<?php echo site_url('Appraisal/add_sub_departments'); ?>",
                    data: {
                        company_id: app.company_id,
                        selected_department_id: selected_department_id,
                        sub_department_description: sub_department_description,
                        sub_department_code: sub_department_code
                    },
                    success: function (data) {
                        stopLoad();
                        $("#CommonEdit_Mod").modal('hide');
                        myAlert('s', '<?php echo $this->lang->line('appraisal_sub_department_saved_successfully'); ?>');/*Sub Department Saved Successfully*/
                        load_sub_departments_table(app.company_id);
                    }
                });
            }
        } else if (app.form_status == 'edit') {
            if (new_sub_department_edit_form_validation()) {
                startLoad();
                var selected_department_id = app.department_id;
                var sub_department_description = $("#subDepartmentDescription").val();
                var sub_department_code = $("#subDepartmentCode").val();
                $.ajax({
                    dataType: "json",
                    type: "POST",
                    url: "<?php echo site_url('Appraisal/edit_sub_departments'); ?>",
                    data: {
                        sub_department_id: app.sub_department_id,
                        sub_department_description: sub_department_description,
                        sub_department_code: sub_department_code
                    },
                    success: function (data) {
                        stopLoad();
                        $("#CommonEdit_Mod").modal('hide');
                        myAlert('s', '<?php echo $this->lang->line('appraisal_sub_department_updated_successfully'); ?>');/*Sub Department Updated Successfully*/
                        load_sub_departments_table(app.company_id);
                    }
                });
            }

        }
    });


    function sub_department_form_hide_errors() {
        hide_error('subDepartmentDescriptionError');
        hide_error('subDepartmentCodeError');
        hide_error('subDepartmentGeneralError');
    }

    function new_sub_department_edit_form_validation() {
        sub_department_form_hide_errors();
        var sub_department_description = $("#subDepartmentDescription").val();
        var sub_department_code = $("#subDepartmentCode").val();

        var status = true;
        if (sub_department_description == "") {
            show_error('subDepartmentDescriptionError', '<?php echo $this->lang->line('appraisal_description_is_required'); ?>')/*Description is required.*/
            status = false;
        }

        if (sub_department_code == "") {
            show_error('subDepartmentCodeError', '<?php echo $this->lang->line('appraisal_code_is_required'); ?>')/*Code is required*/
            status = false;
        }

        $.ajax({
            async: false,
            dataType: "text",
            type: "POST",
            url: "<?php echo site_url('Appraisal/department_name_code_edit_validate'); ?>",
            data: {
                sub_department_description: sub_department_description,
                sub_department_code: sub_department_code,
                department_id: app.department_id,
                sub_department_id: app.sub_department_id
            },
            success: function (data) {
                if (data == 'true') {
                    status = false;
                    show_error('subDepartmentGeneralError', '<?php echo $this->lang->line('appraisal_department_name_or_code_already_used'); ?>.');/*Department Name or Code Already Used*/
                }
            }
        })
        ;
        return status;
    }

    function new_sub_department_form_validation() {
        sub_department_form_hide_errors();
        var sub_department_description = $("#subDepartmentDescription").val();
        var sub_department_code = $("#subDepartmentCode").val();

        var status = true;
        if (sub_department_description == "") {
            show_error('subDepartmentDescriptionError', '<?php echo $this->lang->line('appraisal_description_is_required'); ?>') /*Description is required.*/
            status = false;
        }

        if (sub_department_code == "") {
            show_error('subDepartmentCodeError', '<?php echo $this->lang->line('appraisal_code_is_required'); ?>')/*Code is required*/
            status = false;
        }

        $.ajax({
            async: false,
            dataType: "text",
            type: "POST",
            url: "<?php echo site_url('Appraisal/is_subdepartment_exist'); ?>",
            data: {
                sub_department_description: sub_department_description,
                sub_department_code: sub_department_code,
                department_id: app.department_id
            },
            success: function (data) {
                if (data == 'true') {
                    status = false;
                    show_error('subDepartmentGeneralError', '<?php echo $this->lang->line('appraisal_department_name_or_code_already_used'); ?>');/*Department Name or Code Already Used*/
                }
            }
        });
        return status;
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
