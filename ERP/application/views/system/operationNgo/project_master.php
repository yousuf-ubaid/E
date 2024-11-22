<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('operationngo', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('operationngo_project');
echo head_page($title, false);


/*echo head_page('Projects', false);*/
$date_format_policy = date_format_policy();
$segment_arr = segment_drop();
$revenue_gl = all_revenue_gl_drop();
?>

<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/crm_style.css'); ?>">
<style>
    .form1 {
        width: 250px !important;
    }

    .btn-primary {
        background-color: #34495e;
        border-color: #34495e;
        color: #FFFFFF;
    }

    .table > tbody > tr > td, .table > tbody > tr > th, .table > tfoot > tr > td, .table > tfoot > tr > th, .table > thead > tr > td, .table > thead > tr > th {
        padding: 5px;
        line-height: 1.42857143;
        vertical-align: top;
        border-top: 1px solid #ddd;
    }

    .header {
        color: #000080;
        font-weight: bolder;
        font-size: 13px;
        background-color: #E8F1F4;
    }
    .head-title h2 {
        margin: 0;
        font-size: 14px;
        line-height: 14px;
        text-transform: uppercase;
        display: inline-block;
        padding: 0 8px 0 0;
        background: #fff;
        white-space: nowrap;
        font-weight: bold !important;
        color: #fb6b01;
    }
</style>

<div id="filter-panel" class="collapse filter-panel">
</div>
<div class="row">
    <div class="col-md-5">
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary pull-right"
                onclick="fetchPage('system/operationNgo/create_project',null,'<?php echo $this->lang->line('operationngo_add_new_project'); ?>'/*Add New Project*/,'NGO');">
            <i class="fa fa-plus"></i> <?php echo $this->lang->line('operationngo_new_project'); ?><!--New Project-->
        </button>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-12">
        <div id="projectMaster_view">
        </div>

    </div>
</div>
<!--modal report-->
<div class="modal fade" id="project_subCategory_model" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 50%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Project Sub Category</h4>
            </div>
            <?php echo form_open('', 'role="form" id="project_subCategory_form"'); ?>
            <input type="hidden" id="ngoProjectID_masterID_hn" name="masterID">
            <input type="hidden" id="edit_ngoProjectID" name="ngoProjectID">
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-sm-12">
                        <header class="head-title">
                            <h2 id="project_parentCategory"><!--via JS --></h2>
                        </header>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-4">
                        <label class="title">
                            <?php echo $this->lang->line('operationngo_project_name'); ?><!--Project Name--></label>
                    </div>
                    <div class="form-group col-sm-7">
                               <span class="input-req" title="Required Field"><input type="text" name="projectName"
                                                                                     id="projectName"
                                                                                     class="form-control"
                                                                                     placeholder="<?php echo $this->lang->line('operationngo_project_name'); ?>"
                                                                                     required><span
                                       class="input-req-inner"></span></span><!--Project Name-->
                    </div>
                </div>
                <div class="row hide" style="margin-top: 10px;">
                    <div class="form-group col-sm-4">
                        <label class="title">Segment</label>
                    </div>
                    <div class="form-group col-sm-7">
                                       <span class="input-req" title="Required Field">
                                <?php echo form_dropdown('segmentID', $segment_arr, '',
                                    'class="form-control select2" id="segmentID"'); ?>
                                           <span class="input-req-inner"></span></span>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-4">
                        <label class="title">Estimated Start Date</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <div class="input-group startdateDatepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="startDate" id="startDate" class="form-control dateFields frm_input">
                        </div>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-4">
                        <label class="title">Estimated End Date</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <div class="input-group startdateDatepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="endDate" id="endDate" class="form-control dateFields frm_input">
                        </div>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-4">
                        <label class="title">Detail Description</label>
                    </div>
                    <div class="form-group col-sm-7">
                <span class="input-req" title="Required Field"><textarea class="form-control" id="description"
                                                                         name="description" rows="2"></textarea><span
                        class="input-req-inner"></span></span>
                    </div>
                </div>
                <div class="row hide" style="margin-top: 10px;">
                    <div class="form-group col-sm-4">
                        <label class="title">Revenue GL</label>
                    </div>
                    <div class="form-group col-sm-7">
                                     <span class="input-req" title="Required Field">
                                <?php echo form_dropdown('revenueGLAutoID', $revenue_gl, '',
                                    'class="form-control select2" id="revenueGLAutoID"'); ?>
                                         <span class="input-req-inner"></span></span>
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
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var Otable;
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/operationNgo/project_master', '', 'Projects');
        });
        getDonorProjectTable();

        $('.startdateDatepic').datetimepicker({
            showTodayButton: true,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
        });

        $('#project_subCategory_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid',
            excluded: [':disabled'],
            fields: {
                projectName: {validators: {notEmpty: {message: 'Project Name is required.'}}},
                description: {validators: {notEmpty: {message: 'Detail Description is required.'}}},
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
                url: "<?php echo site_url('OperationNgo/save_ngo_project_subcategory'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        getDonorProjectTable();
                        $('#project_subCategory_model').modal('hide');
                    } else {
                        $('.btn-primary').removeAttr('disabled');
                    }

                },
                error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });

    });

    function getDonorProjectTable(filtervalue) {
        var searchTask = $('#searchTask').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'searchTask': searchTask, 'filtervalue': filtervalue},
            url: "<?php echo site_url('OperationNgo/load_donorProjectView'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#projectMaster_view').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function delete_ngo_project(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>", /*You want to delete this record!*/
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
                    data: {'ngoProjectID': id},
                    url: "<?php echo site_url('OperationNgo/delete_ngo_project'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        refreshNotifications(true);
                        stopLoad();
                        getDonorProjectTable();
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }


    function project_SubCategoryAdd(masterID,description) {
        $('#project_subCategory_form')[0].reset();
        $('#project_subCategory_form').bootstrapValidator('resetForm', true);
        $("#project_parentCategory").html(description);
        $('#ngoProjectID_masterID_hn').val(masterID);
        $('#btn-add-subCategory').html('Save');
        $('#edit_ngoProjectID').val('');
        $("#project_subCategory_model").modal({backdrop: "static"});
    }
    


    function project_SubCategoryEdit(ngoProjectID) {
        $('#project_subCategory_form').bootstrapValidator('resetForm', true);
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
                    $('#projectName').val(data['projectName']);
                    $('#description').val(data['description']);
                    $('#startDate').val(data['startDate']);
                    $('#endDate').val(data['endDate']);
                    $('#ngoProjectID_masterID_hn').val(data['masterID']);
                    $('#edit_ngoProjectID').val(data['ngoProjectID']);
                    $('#btn-add-subCategory').html('Update');
                    $("#project_subCategory_model").modal({backdrop: "static"});
                }
                stopLoad();
                refreshNotifications(true);
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }


</script>