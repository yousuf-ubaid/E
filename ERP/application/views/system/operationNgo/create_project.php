<?php
$primaryLanguage = getPrimaryLanguage();

$this->lang->load('operationngo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

echo head_page($_POST['page_name'], FALSE);
$this->load->helper('operation_ngo_helper');
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$all_states_arr = all_states();
$countries_arr = load_all_countrys();
$countryCode_arr = all_country_codes();

$segment_arr = segment_drop();
$revenue_gl = all_revenue_gl_drop();
$employeedrop = all_employee_dropngo();
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
<div id="filter-panel" class="collapse filter-panel"></div>
<style>
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        color: #060606
    }

    span.input-req-inner {
        width: 20px;
        height: 40px;
        position: absolute;
        overflow: hidden;
        display: block;
        right: 4px;
        top: -15px;
        -webkit-transform: rotate(135deg);
        -ms-transform: rotate(135deg);
        transform: rotate(135deg);
    }

    span.input-req-inner:before {
        font-size: 20px;
        content: "*";
        top: 15px;
        right: 1px;
        color: #fff;
        position: absolute;
        z-index: 2;
        cursor: default;
    }

    span.input-req-inner:after {
        content: '';
        width: 35px;
        height: 35px;
        -webkit-transform: rotate(45deg);
        -ms-transform: rotate(45deg);
        transform: rotate(45deg);
        background: #f45640;
        position: absolute;
        top: 7px;
        right: -29px;
    }

    .search_cancel {
        background-color: #f3f3f3;
        border: solid 1px #dcdcdc;
        vertical-align: middle;
        padding: 3px;
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        border-radius: 2px;
    }

    .nav-tabs > li > a {
        font-size: 11px;
        line-height: 30px;
        height: 30px;
        position: relative;
        padding: 0 25px;
        float: left;
        display: block;
        /*color: rgb(44, 83, 158);*/
        letter-spacing: 1px;
        text-transform: uppercase;
        font-weight: bold;
        text-align: center;
        text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.3);
        color: rgb(130, 130, 130);
    }

    .nav-tabs > li > a:hover {
        background: rgb(230, 231, 234);
        font-size: 12px;
        line-height: 30px;
        height: 30px;
        position: relative;
        padding: 0 25px;
        float: left;
        display: block;
        /*color: rgb(44, 83, 158);*/
        letter-spacing: 1px;
        text-transform: uppercase;
        font-weight: bold;
        text-align: center;
        border-radius: 3px 3px 0 0;
        border-color: transparent;
    }

    .nav-tabs > li.active > a,
    .nav-tabs > li.active > a:hover,
    .nav-tabs > li.active > a:focus {
        color: #c0392b;
        cursor: default;
        background-color: #fff;
        font-weight: bold;
        border-bottom: 3px solid #f15727;
    }
</style>

<div class="hide project_assignusers" style="margin-top: 1%;">
    <ul class="nav nav-tabs" id="main-tabs">
        <li class="active"><a href="#project" data-toggle="tab"><i class="fa fa-television"></i>Project</a></li>
        <li><a href="#userassign" data-toggle="tab"><i class="fa fa-television"></i>Assign Users</a></li>

    </ul>

</div>
<div class="tab-content">
    <div class="tab-pane active" id="project">
        <br>
        <?php echo form_open('', 'role="form" id="contact_form"'); ?>
        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2>
                        <?php echo $this->lang->line('operationngo_project_name_and_detail'); ?><!--PROJECT NAME AND DETAIL--></h2>
                </header>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-1">
                        &nbsp
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title">
                            <?php echo $this->lang->line('operationngo_project_name'); ?><!--Project Name--></label>
                    </div>
                    <div class="form-group col-sm-4">
                               <span class="input-req" title="Required Field"><input type="text" name="projectName"
                                                                                     id="projectName"
                                                                                     class="form-control"
                                                                                     placeholder="<?php echo $this->lang->line('operationngo_project_name'); ?>"
                                                                                     required><span
                                           class="input-req-inner"></span></span><!--Project Name-->
                        <input type="hidden" name="ngoProjectID" id="ngoProjectID_edit">
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-1">
                        &nbsp
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('common_segment'); ?></label><!--Segment-->
                    </div>
                    <div class="form-group col-sm-4">
                                     <span class="input-req" title="Required Field">
                                <?php echo form_dropdown('segmentID', $segment_arr, '',
                                    'class="form-control select2" id="segmentID"'); ?>
                                         <span class="input-req-inner"></span></span>
                    </div>

                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-1">
                        &nbsp
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('common_description'); ?></label>
                        <!--Description-->
                    </div>
                    <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field"><textarea class="form-control" id="description"
                                                                         name="description" rows="2"></textarea><span
                            class="input-req-inner"></span></span>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-1">
                        &nbsp
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title">
                            <?php echo $this->lang->line('operationngo_revenue_gl'); ?><!--Revenue GL--></label>
                    </div>
                    <div class="form-group col-sm-4">
                                     <span class="input-req" title="Required Field">
                                <?php echo form_dropdown('revenueGLAutoID', $revenue_gl, '',
                                    'class="form-control select2" id="revenueGLAutoID"'); ?>
                                         <span class="input-req-inner"></span></span>
                    </div>

                </div>


            </div>

        </div>
        <div class="row">
            <div class="form-group col-sm-1" style="margin-top: 10px;">
                &nbsp
            </div>
            <div class="form-group col-sm-6">
                <div class="text-right m-t-xs">
                    <button class="btn btn-primary" type="submit">
                        <?php echo $this->lang->line('common_save'); ?><!--Save--></button>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div id="userassign" class="tab-pane">
        <div class="row addTableView">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2>USER ASSIGN</h2>
                </header>
                <div class="row">
                    <div class="col-sm-12">
                        <button type="button" class="btn btn-primary pull-right"
                                onclick="open_user_assign_modal()">
                            <i class="fa fa-plus"></i> Assign Users
                        </button>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="col-sm-11">
                        <div id="userassigntable"></div>
                    </div>
                    <div class="col-sm-1">
                        &nbsp;
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</form>
<div class="modal fade" id="add_employees_model" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 50%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Add Employees </h4>
            </div>
            <?php echo form_open('', 'role="form" id="employees_form"'); ?>
            <div class="modal-body">
                <div class="row">
                    <input type="hidden" name="project" id="projectid">
                    <div class="form-group col-sm-4">
                        <label class="title">Employee Name</label>
                    </div>
                    <div class="form-group col-sm-6">

                        <?php echo form_dropdown('employee', $employeedrop, '', 'class="form-control" id="employee" '); ?>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-primary" type="submit" id="btn-add-subCategory">Save</button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
        </form>
    </div>
</div>

<script src="<?php echo base_url('plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js'); ?>"></script>
<script type="text/javascript">
    var ngoProjectID = null;
    $(document).ready(function () {

        $('.headerclose').click(function () {

            fetchPage('system/operationNgo/project_master', '', 'Projects');
        });

        Inputmask().mask(document.querySelectorAll("input"));

        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });

        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {

            ngoProjectID = p_id;

            load_donor_header();
            fetch_user_assign();

        }
        $('#contact_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.', /*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                projectName: {validators: {notEmpty: {message: 'Project Name is required.'}}},
                segmentID: {validators: {notEmpty: {message: 'Segment is required.'}}},
                description: {validators: {notEmpty: {message: 'Description is required.'}}},
                revenueGLAutoID: {validators: {notEmpty: {message: 'Revenue GL is required.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('OperationNgo/save_donor_project'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        fetchPage('system/operationNgo/project_master', '', 'Projects');
                    } else {
                        $('.btn-primary').removeAttr('disabled');
                    }

                },
                error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });

    });


    function load_donor_header() {
        if (ngoProjectID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'ngoProjectID': ngoProjectID},
                url: "<?php echo site_url('OperationNgo/load_donor_project_data'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        $('.project_assignusers').removeClass('hide');
                        $('#ngoProjectID_edit').val(ngoProjectID);
                        $('#projectName').val(data['projectName']);
                        $('#description').val(data['description']);
                        $('#projectid').val(data['ngoProjectID']);
                        $('#revenueGLAutoID').val(data['revenueGLAutoID']).change();
                        $('#segmentID').val(data['segmentID']).change();

                    }
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }

    function fetch_user_assign() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {ngoProjectID: ngoProjectID},
            url: "<?php echo site_url('OperationNgo/load_user_assign_table'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#userassigntable').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }


    $('#employees_form').bootstrapValidator({
        live: 'enabled',
        message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.', /*This value is not valid*/
        excluded: [':disabled'],
        fields: {
            employee: {validators: {notEmpty: {message: 'Employee is required.'}}},
        },
    }).on('success.form.bv', function (e) {
        e.preventDefault();
        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
        var data = $form.serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('OperationNgo/save_employees'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    fetch_user_assign();
                    $('#add_employees_model').modal('hide');
                } else {
                    $('.btn-primary').removeAttr('disabled');
                }

            },
            error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    })

    function delete_assign_user_ngo_projects(projectOwnerID) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                text: "You want to delete this user !", /*You want to delete this record!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>", /*Delete*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'projectOwnerID': projectOwnerID},
                    url: "<?php echo site_url('OperationNgo/delete_assign_usersfor_project'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data['status'] == 1) {
                            myAlert('e', data['message']);
                        } else if (data['status'] == 0) {
                            myAlert('s', data['message']);
                            fetch_user_assign();
                        } else {
                            myAlert('e', 'Deletion Failed');
                        }
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });

    }

    function open_user_assign_modal() {

        $('#employees_form').bootstrapValidator('resetForm', true);
        $('#employees_form')[0].reset();
        $('#employee')
        $('#add_employees_model').modal('show');
    }

</script>